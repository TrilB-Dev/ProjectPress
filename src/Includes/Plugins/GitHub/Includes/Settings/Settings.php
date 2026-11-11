<?php
/**
 * Settings for the Font Awesome ProjectPress plugin.
 * 
 * @package    ProjectPress
 * @subpackage ProjectPress/includes
 */
namespace ProjectPress\Includes\Plugins\GitHub\Includes\Settings;
use ProjectPress\Includes\Settings\Settings as BaseSettings;
use ProjectPress\Includes\Functions\Helpers\SanitizationHelper;

final class Settings {
    /**
     * Registers the GitHub settings group.
     *
     * @since 1.0.0
     */
    public function register(): void {
        BaseSettings::register_group( 'github', [
            'github_access_token' => '',
        ] );
    }

    /**
     * Retrieves the GitHub access token.
     *
     * @return string The GitHub access token.
     */
    public static function get_token(): string {
        $token = BaseSettings::get_key( 'github_access_token', '' );
        return ! empty( $token ) ? $token : '';
    }
    /**
     * Retrieves the settings page configuration for the GitHub plugin.
     *
     * @return array The settings page configuration.
     * @since 1.0.0
     */
    public function get_settings_page(): array {
        return [
            'slug' => 'github',
            'label' => __( 'GitHub', 'projectpress' ),
            'title' => __( 'GitHub Integration', 'projectpress' ),
            'layout' => 'table',
            'fields' => [
                [
                    'key' => 'github_access_token',
                    'label' => __( 'GitHub Token', 'projectpress' ),
                    'description' => __( 'Enter your GitHub access token for ProjectPress.', 'projectpress' ),
                    'tooltip' => __( 'Provide your GitHub access token to enable integration with ProjectPress.', 'projectpress' ),
                    'tooltip_type' => 'info',
                    'type' => 'password',
                    'class' => 'w-100',
                    'default' => '',
                ]
            ],
        ];
    }
    /**
     * Sanitizes the input for the GitHub settings.
     *
     * @param array $input The input array to sanitize.
     * @return array The sanitized input array.
     * @since 1.0.0
     */
    public function sanitize( $input ): array {
        $input = is_array( $input ) ? $input : [];

        $token = SanitizationHelper::key( $input['github_access_token'] ?? '', '' );
        $input['github_access_token'] = $token;


        BaseSettings::set_group( 'github', $input );
        return $input;
    }
}

