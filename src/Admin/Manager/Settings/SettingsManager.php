<?php
/**
 * Settings manager for ProjectPress.
 *
 * @package ProjectPress
 * @subpackage Admin\Manager\Settings
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\Settings;

use ProjectPress\Admin\Manager\Manager;
use ProjectPress\Assets\Assets;
use ProjectPress\Includes\Functions\Admin\FunctionsPlugins;
use ProjectPress\Includes\Functions\Admin\FunctionsSettings;
use ProjectPress\Includes\Functions\Helpers\AlertHelper;
use ProjectPress\Includes\Functions\Helpers\SanitizationHelper;
use ProjectPress\Includes\Settings\CoreSettings;
use ProjectPress\Includes\Settings\Settings;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsManager extends Manager {
	private SettingsGeneral $general_page;
	private SettingsProjects $projects_page;
	private SettingsLayout $layout_page;
	private SettingsAccess $access_page;
	private SettingsNotifications $notifications_page;
	private SettingsActivity $activity_page;
	private SettingsPlugins $plugins_page;
	private FunctionsSettings $settings_functions;
	private FunctionsPlugins $plugin_functions;

	public function __construct() {
		$this->page               = 'settings';
		$this->general_page       = new SettingsGeneral();
		$this->projects_page      = new SettingsProjects();
		$this->layout_page        = new SettingsLayout();
		$this->access_page        = new SettingsAccess();
		$this->notifications_page = new SettingsNotifications();
		$this->activity_page      = new SettingsActivity();
		$this->plugins_page       = new SettingsPlugins();
		$this->plugin_functions   = new FunctionsPlugins();
		$this->settings_functions = new FunctionsSettings( $this->plugin_functions );
		CoreSettings::register();
		$this->register_actions();
	}

	private function register_actions(): void {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'admin_post_projectpress_save_settings', array( $this->settings_functions, 'save_settings' ) );
		add_action( 'admin_post_projectpress_save_general_settings', array( $this->settings_functions, 'general_save' ) );
		add_action( 'admin_post_projectpress_save_projects_settings', array( $this->settings_functions, 'projects_save' ) );
		add_action( 'admin_post_projectpress_save_access_settings', array( $this->settings_functions, 'access_save' ) );
		add_action( 'admin_post_projectpress_save_layout_settings', array( $this->settings_functions, 'layout_save' ) );
		add_action( 'admin_post_projectpress_save_notifications_settings', array( $this->settings_functions, 'notifications_save' ) );
		add_action( 'admin_post_projectpress_save_activity_settings', array( $this->settings_functions, 'activity_save' ) );
	}

	public function register_rest_routes(): void {
		register_rest_route(
			'projectpress/v1',
			'/settings/tab/(?P<tab>[a-z0-9_\-]+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'rest_get_tab' ),
				'permission_callback' => static fn(): bool => current_user_can( 'manage_options' ) || current_user_can( 'projectpress_settings_general_view' ),
				'args'                => array(
					'tab'            => array( 'required' => true, 'sanitize_callback' => 'sanitize_key' ),
					'layout_section' => array( 'required' => false, 'sanitize_callback' => 'sanitize_key' ),
				),
			)
		);
		register_rest_route( 'projectpress/v1', '/plugins/toggle', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( $this->plugin_functions, 'rest_toggle_plugin' ), 'permission_callback' => static fn(): bool => current_user_can( 'manage_options' ) || current_user_can( 'projectpress_settings_plugins_int_edit' ) || current_user_can( 'projectpress_settings_plugins_ext_edit' ) ) );
		register_rest_route( 'projectpress/v1', '/plugins/settings', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( $this->plugin_functions, 'rest_save_plugin_settings' ), 'permission_callback' => static fn(): bool => current_user_can( 'manage_options' ) || current_user_can( 'projectpress_settings_plugins_int_edit' ) || current_user_can( 'projectpress_settings_plugins_ext_edit' ) ) );
	}

	public function rest_get_tab( WP_REST_Request $request ): WP_REST_Response {
		$tab            = $this->normalize_tab( (string) $request->get_param( 'tab' ) );
		$layout_section = SanitizationHelper::key( (string) ( $request->get_param( 'layout_section' ) ?: 'general' ), 'general' );
		ob_start();
		$this->render_tab_content( $tab, $layout_section );
		return rest_ensure_response( array( 'tab' => $tab, 'layout_section' => $layout_section, 'html' => (string) ob_get_clean() ) );
	}

	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, array( 'projectpress' ), 'settings' );
		$this->register_page_assets( $assets, array( 'projectpress' ), 'plugins' );
	}

	public function render(): void {
		$tab            = $this->normalize_tab( SanitizationHelper::key( wp_unslash( $_GET['tab'] ?? 'general' ), 'general' ) );
		$layout_section = SanitizationHelper::key( wp_unslash( $_GET['s'] ?? $_GET['layout_section'] ?? 'general' ), 'general' );
		$this->header( __( 'Settings', 'projectpress' ) );
		if ( ! empty( $_GET['settings_saved'] ) ) {
			echo AlertHelper::get_admin_notice( isset( $_GET['settings_message'] ) ? rawurldecode( wp_unslash( $_GET['settings_message'] ) ) : __( 'ProjectPress settings saved successfully.', 'projectpress' ), 'success' );
		} elseif ( ! empty( $_GET['settings_failed'] ) ) {
			echo AlertHelper::get_admin_notice( isset( $_GET['settings_message'] ) ? rawurldecode( wp_unslash( $_GET['settings_message'] ) ) : __( 'Failed to save ProjectPress settings.', 'projectpress' ), 'error' );
		}
		echo '<div id="projectpress-settings-panel" data-current-tab="' . esc_attr( $tab ) . '">';
		$this->render_tab_content( $tab, $layout_section );
		echo '</div>';
		$this->footer();
	}

	public function render_tab_content( string $tab, string $layout_section = 'general' ): void {
		$tab = $this->normalize_tab( $tab );
		if ( in_array( $tab, array( 'plugins', 'third-party' ), true ) ) {
			$this->plugins_page->render( $tab );
			return;
		}
		$values = Settings::get_group( $tab, array() );
		switch ( $tab ) {
			case 'general': $this->general_page->render( $values ); break;
			case 'projects': $this->projects_page->render( $values ); break;
			case 'layout': $this->layout_page->render( $values, $layout_section ); break;
			case 'access': $this->access_page->render( $values ); break;
			case 'notifications': $this->notifications_page->render( $values ); break;
			case 'activity': $this->activity_page->render( $values ); break;
			default:
				if ( $this->plugins_page->has_settings_page( $tab ) ) {
					$this->plugins_page->render_settings_page( $tab, Settings::get_all() );
				}
		}
	}

	private function normalize_tab( string $tab ): string {
		$allowed = array( 'general', 'projects', 'layout', 'access', 'notifications', 'activity', 'tools', 'plugins', 'third-party' );
		return in_array( $tab, $allowed, true ) || $this->plugins_page->has_settings_page( $tab ) ? $tab : 'general';
	}
}
