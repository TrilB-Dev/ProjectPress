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
     * Registers the Font Awesome settings group.
     *
     * @since 1.0.0
     */
    public function register(): void {
        BaseSettings::register_group( 'github', [
            'github_type' => 'cdn',
            'github_cdn_technology' => 'webfont',
            'github_kit_id' => '',
            'github_enable_icon_picker' => true,
        ] );
    }
    /**
     * Determines whether the icon picker is enabled.
     *
     * @return bool True if the icon picker is enabled, false otherwise.
     * @since 1.0.0
     */
    public static function enable_icon_picker(): bool {
        $enabled = BaseSettings::get_bool( 'github_enable_icon_picker', true );
        if ( ! $enabled ) {
            $enabled = BaseSettings::get_bool( 'github_icon_picker', false );
        }

        return $enabled;
    }

    /**
     * Retrieves the selected Font Awesome source type.
     *
     * @return string The source type.
     */
    public static function get_type(): string {
        $type = BaseSettings::get_key( 'github_type', 'cdn' );
        return in_array( $type, [ 'cdn', 'kit' ], true ) ? $type : 'cdn';
    }

    /**
     * Retrieves the configured Font Awesome kit ID.
     *
     * @return string The Font Awesome kit ID.
     */
    public static function get_kit_id(): string {
        return BaseSettings::get_string( 'github_kit_id', '' );
    }

    /**
     * Retrieves the selected Font Awesome CDN technology.
     *
     * @return string The CDN technology.
     */
    public static function get_cdn_technology(): string {
        $technology = BaseSettings::get_key( 'github_cdn_technology', 'webfont' );
        return in_array( $technology, [ 'webfont', 'svg' ], true ) ? $technology : 'webfont';
    }
    /**
     * Retrieves the settings page configuration for the Font Awesome plugin.
     *
     * @return array The settings page configuration.
     * @since 1.0.0
     */
    public function get_settings_page(): array {
        return [
            'slug' => 'github',
            'label' => __( 'GitHub', 'projectpress' ),
            'title' => __( 'GitHub integration', 'projectpress' ),
            'layout' => 'table',
            'fields' => [
                [
                    'key' => 'github_type',
                    'label' => __( 'GitHub Type', 'projectpress' ),
                    'description' => __( 'Select how github is called in ProjectPress.', 'projectpress' ),
                    'tooltip' => __( 'Choose between using the CDN or a Kit for GitHub.', 'projectpress' ),
                    'tooltip_type' => 'info',
                    'type' => 'select',
                    'class' => 'w-100',
                    'options' => [
                        'cdn' => __( 'CDN', 'projectpress' ),
                        'kit' => __( 'Kit', 'projectpress' ),
                    ],
                    'default' => 'cdn',
                ],
                [
                    'key' => 'github_cdn_technology',
                    'label' => __( 'GitHub CDN Technology', 'projectpress' ),
                    'description' => __( 'Select the technology used for the Font Awesome CDN.', 'projectpress' ),
                    'tooltip' => __( 'Choose between different CDN technologies for loading Font Awesome.', 'projectpress' ),
                    'tooltip_type' => 'info',
                    'type' => 'select',
                    'class' => 'w-100',
                    'options' => [
                        'svg' => __( 'SVG', 'projectpress' ),
                        'webfont' => __( 'Web Font', 'projectpress' ),
                    ],
                    'default' => 'webfont',
                    'visible_when' => [
                        'github_type' => 'cdn',
                    ],
                    'required' => [
                        'github_type' => 'cdn',
                    ],

                ],
                [
                    'key' => 'github_kit_id',
                    'label' => __( 'GitHub Kit ID', 'projectpress' ),
                    'description' => __( 'Enter the ID of your Font Awesome Kit if you selected "Kit" as the type.', 'projectpress' ),
                    'tooltip' => __( 'The GitHub Kit ID is required when using the Kit type.', 'projectpress' ),
                    'tooltip_type' => 'info',
                    'type' => 'text',
                    'class' => 'w-100',
                    'default' => '',
                    'visible_when' => [
                        'github_type' => 'kit',
                    ],
                    'required' => [
                        'github_type' => 'kit',
                    ],
                ],
                [
                    'key' => 'github_enable_icon_picker',
                    'label' => __( 'Enable icon picker', 'projectpress' ),
                    'description' => __( 'Enable the bundled Font Awesome icon picker inside ProjectPress admin interfaces.', 'projectpress' ),
                    'tooltip' => __( 'ProjectPress includes its own icon picker and fallback icon catalog, so no WordPress GitHub plugin is required.', 'projectpress' ),
                    'tooltip_type' => 'info',
                    'type' => 'switch',
                    'default' => true,
                ],
            ],
        ];
    }
    /**
     * Sanitizes the input for the Font Awesome settings.
     *
     * @param array $input The input array to sanitize.
     * @return array The sanitized input array.
     * @since 1.0.0
     */
    public function sanitize( $input ): array {
        $input = is_array( $input ) ? $input : [];

        $type = SanitizationHelper::key( $input['github_type'] ?? 'cdn', 'cdn' );
        $input['github_type'] = in_array( $type, [ 'cdn', 'kit' ], true ) ? $type : 'cdn';

        if ( 'kit' === $input['github_type'] ) {
            $input['github_kit_id'] = trim( (string) ( $input['github_kit_id'] ?? '' ) );
        } else {
            $input['github_kit_id'] = '';
        }

        $legacy_picker = ! empty( $input['github_icon_picker'] );
        $input['github_enable_icon_picker'] = (bool) ( $input['github_enable_icon_picker'] ?? $legacy_picker );
        $input['github_icon_picker'] = $input['github_enable_icon_picker'];

        BaseSettings::set_group( 'github', $input );
        return $input;
    }
}

