<?php
/**
 * GitHub plugin integration for ProjectPress.
 *
 * @package GitHub
 * @textdomain projectpress
 * @domainpath Languages
 * @author ProjectPress Team
 */

namespace ProjectPress\Includes\Plugins\GitHub;

use ProjectPress\Includes\Plugins\AssetsProviderInterface;
use ProjectPress\Includes\Plugins\I18nProviderInterface;
use ProjectPress\Includes\Plugins\PluginInterface;
use ProjectPress\Includes\Plugins\SettingsProviderInterface;
use ProjectPress\Includes\Plugins\SettingsPageProviderInterface;
use ProjectPress\Includes\Plugins\GitHub\Assets\Assets;
use ProjectPress\Includes\Plugins\GitHub\Includes\Core\I18n;
use ProjectPress\Includes\Plugins\GitHub\Includes\Includes;
use ProjectPress\Includes\Core\WP\Activator;

final class GitHub implements PluginInterface, SettingsProviderInterface, SettingsPageProviderInterface, AssetsProviderInterface, I18nProviderInterface {
    /**
     * Singleton instance of the GitHub plugin.
     *
     * @var self|null
     * @since 1.0.0
     */
    private static ?self $instance = null;

    /**
     * Get the plugin slug.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_slug(): string {
        return 'projectpress-github';
    }

    /**
     * Get the plugin name.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_name(): string {
        return 'GitHub';
    }

    /**
     * Get the plugin icon.
     *
     * @return array{0: string, 1: string}
     * @since 1.0.0
     */
    public function get_icon(): array {
        return [ 'fab fa-github', 'rgb(36, 40, 43)' ];
    }

    /**
     * Get the plugin version.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_version(): string {

        return '1.0.0';
    }

    /**
     * Get the plugin author.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_author(): string {
        return 'TrilB.Dev Team';
    }

    /**
     * Get the plugin author URI.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_author_uri(): string {
        return 'https://trilb.dev/';
    }

    /**
     * Get the plugin description.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_description(): string {
        return __( 'Provides GitHub integration for ProjectPress.', 'projectpress' );
    }

    /**
     * Get the plugin URI.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_uri(): string {
        return 'https://trilb.dev/collection/web-extension/wordpress/projectpress';
    }

    /**
     * Get the plugin license.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_license(): string {
        return 'GPL-2.0-or-later';
    }

    /**
     * Check if the plugin is active.
     *
     * @return bool
     * @since 1.0.0
     */
    public function is_active(): bool {
        return true;
    }

    /**
     * Private constructor to prevent direct instantiation.
     *
     * @since 1.0.0
     */
    private function __construct() {
    }
    /**
     * Initialize the GitHub plugin.
     *
     * @return void
     * @since 1.0.0
     */
    public function init(): void {
        Includes::get_instance()->init();
    }

    /**
     * Register plugin settings.
     *
     * @return void
     * @since 1.0.0
     */
    public function register_settings(): void {
        Includes::get_instance()->settings()->register();
    }

    /**
     * Get the settings page config.
     *
     * @return array
     * @since 1.0.0
     */
    public function get_settings_page(): array {
        return Includes::get_instance()->settings()->get_settings_page();
    }

    /**
     * Sanitize plugin settings.
     *
     * @param mixed $input
     * @return array
     * @since 1.0.0
     */
    public function sanitize_settings( $input ): array {
        return Includes::get_instance()->settings()->sanitize( $input );
    }

    /**
     * Register plugin assets.
     *
     * @return void
     * @since 1.0.0
     */
    public function register_assets(): void {
        ( new Assets() )->register();
    }

    /**
     * Load the text domain.
     *
     * @return void
     * @since 1.0.0
     */
    public function load_textdomain(): void {
        I18n::load_textdomain();
    }

    /**
     * Get the singleton instance.
     *
     * @return self
     * @since 1.0.0
     */
    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

}


