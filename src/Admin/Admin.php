<?php
/**
 * Admin class for ProjectPress plugin.
 *
 * @package ProjectPress
 * @subpackage Admin
 * @since 1.0.0
 */
namespace ProjectPress\Admin;

use ProjectPress\Includes\Settings\Settings;
use ProjectPress\Includes\Functions\Helpers\AjaxHelper;
use ProjectPress\Includes\Core\Capabilities;
use ProjectPress\Includes\Functions\Helpers\LoaderHelper;
use ProjectPress\Includes\Functions\Helpers\LoggerHelper;
use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Functions\Helpers\SanitizationHelper;
use ProjectPress\Includes\Functions\Admin\FunctionsSidebar;
use ProjectPress\Assets\Assets;
use ProjectPress\Admin\Manager\Manager;
use ProjectPress\Admin\Manager\Tools\ToolsManager;
use ProjectPress\Admin\Manager\Dashboard\DashboardManager;
use ProjectPress\Admin\Manager\ProjectManager\ProjectsManager;
use ProjectPress\Admin\Manager\Mods\ModManager;
use ProjectPress\Admin\Manager\Settings\SettingsManager;
use ProjectPress\Admin\Manager\Tools\Export;
use ProjectPress\Admin\Manager\Tools\General;
use ProjectPress\Admin\Manager\Tools\Import;
use ProjectPress\Admin\Manager\Tools\Reset;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Admin {
	private DashboardManager $dashboard_manager;
	private SettingsManager $settings_manager;
	private ModManager $mod_manager;
	private ProjectsManager $projects_manager;
	private ToolsManager $tools_manager;
	/** @var array<string, Manager> */
	private array $managers;
	private LoaderHelper $loader;
	private Assets $assets;

	public function __construct( Assets $assets ) {
		$this->managers = array(
			'dashboard' => new DashboardManager(),
			'project-manager' => new ProjectsManager(),
			'settings' => new SettingsManager(),
			'mod-manager' => new ModManager(),
			'tools' => new ToolsManager(),
		);
		$this->dashboard_manager = $this->managers['dashboard'];
		$this->projects_manager = $this->managers['project-manager'];
		$this->settings_manager = $this->managers['settings'];
		$this->mod_manager = $this->managers['mod-manager'];
		$this->tools_manager = $this->managers['tools'];
		$this->loader = new LoaderHelper();
		new General( $this->loader ); new Reset( $this->loader ); new Import( $this->loader ); new Export( $this->loader );
		$this->assets = $assets;
		foreach ( $this->managers as $manager ) { $manager->register_assets( $assets ); }
		$this->loader->register_component( $this, array( array( 'type' => 'action', 'hook' => 'wp_ajax_projectpress_dismiss_onboarding', 'callback' => 'dismiss_onboarding' ) ) );
		$this->loader->run();
	}

	public function register_admin_menu(): void {
		LoggerHelper::write_log( 'ProjectPress admin menu registration started.' );
		try { FunctionsSidebar::register_admin_menu( $this ); LoggerHelper::write_log( 'ProjectPress admin menu registration complete.' ); }
		catch ( \Throwable $e ) { LoggerHelper::write_log( 'ProjectPress admin menu registration failed: ' . $e->getMessage() ); if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) { wp_die( esc_html( $e->getMessage() ), __( 'ProjectPress admin menu error', 'projectpress' ), array( 'back_link' => true ) ); } }
	}

	public function render_dashboard(): void { $this->route_manager( RequestHelper::get_key( 'group', '' ), RequestHelper::get_key( 'tab', '' ) ); }

	public function route_manager( string $group, string $tab = '', string $form = '' ): void {
		$group = sanitize_key( $group ); $tab = sanitize_key( $tab ); $form = sanitize_key( $form );
		$normalized_group = array( 'projectpress' => 'dashboard', 'dashboard' => 'dashboard', 'projects' => 'project-manager', 'project-manager' => 'project-manager', 'mods' => 'mods', 'mod-manager' => 'mods', 'manage-mod' => 'mods', 'manage-mods' => 'mods', 'settings' => 'settings', 'tools' => 'tools', 'reports' => 'reports' )[ $group ] ?? 'dashboard';
		try {
			switch ( $normalized_group ) {
				case 'project-manager': $this->render_projects( $tab, $form ); return;
				case 'mods': $this->render_mods( $tab, $form ); return;
				case 'settings': $this->render_settings(); return;
				case 'tools': $this->render_tools(); return;
				default: $this->dashboard_manager->render();
			}
		} catch ( \Throwable $e ) { LoggerHelper::write_log( 'ProjectPress dashboard render failed: ' . $e->getMessage() ); if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) { wp_die( esc_html( $e->getMessage() ), __( 'ProjectPress dashboard error', 'projectpress' ), array( 'back_link' => true ) ); } }
	}

	public function dismiss_onboarding(): void {
		if ( ! AjaxHelper::authorized( 'projectpress_dismiss_onboarding', 'manage_options' ) ) { AjaxHelper::unauthorized( __( 'You are not authorized to dismiss the ProjectPress onboarding modal.', 'projectpress' ) ); }
		Settings::register_group( 'setup', array( 'first_install_complete' => false ) ); Settings::set( 'first_install_complete', true ); Settings::set( 'onboarding_steps_complete', 3 ); AjaxHelper::success( array( 'dismissed' => true ) );
	}
	public function render_projects( string $tab = '', string $form = '' ): void { $this->projects_manager->render( $tab, $form ); }
	public function render_mods( string $tab = '', string $form = '' ): void { $this->mod_manager->render( $tab, $form ); }
	public function render_settings(): void { $this->settings_manager->render(); }
	public function render_tools(): void { $this->tools_manager->render(); }
	public function capability( string $key, string $fallback ): string {
		$value = Settings::get( $key, $fallback ); $values = is_array( $value ) ? $value : array( $value );
		$allowed = array_merge( array( 'manage_options', 'edit_posts', 'publish_posts', 'manage_categories', 'delete_posts' ), array_keys( Capabilities::definitions() ) );
		foreach ( $values as $value ) { $capability = SanitizationHelper::key( $value, $fallback ); if ( in_array( $capability, $allowed, true ) ) { return $capability; } }
		return $fallback;
	}
}
