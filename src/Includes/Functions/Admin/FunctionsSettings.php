<?php
/**
 * Settings-related admin functions for ProjectPress.
 *
 * @package ProjectPress
 * @subpackage Includes\Functions\Admin
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Functions\Admin;

use ProjectPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class FunctionsSettings {
	private FunctionsPlugins $plugin_functions;

	public function __construct( FunctionsPlugins $plugin_functions ) {
		$this->plugin_functions = $plugin_functions;
	}

	public function register_settings(): void {}

	public function save_settings(): void {
		$action = isset( $_POST['action'] ) ? sanitize_key( wp_unslash( $_POST['action'] ) ) : '';
		$map = array(
			'projectpress_save_general_settings'       => 'general_save',
			'projectpress_save_projects_settings'      => 'projects_save',
			'projectpress_save_layout_settings'        => 'layout_save',
			'projectpress_save_access_settings'        => 'access_save',
			'projectpress_save_notifications_settings' => 'notifications_save',
			'projectpress_save_activity_settings'      => 'activity_save',
		);
		if ( isset( $map[ $action ] ) ) {
			$this->{$map[ $action ]}();
		}
	}

	public function general_save(): void {
		$this->save_group( Settings::GENERAL, 'projectpress_general', 'projectpress_save_general_settings', array( $this, 'sanitize_general' ) );
	}

	public function projects_save(): void {
		$this->save_group( Settings::PROJECTS, 'projectpress_projects', 'projectpress_save_projects_settings', array( $this, 'sanitize_projects' ) );
	}

	public function layout_save(): void {
		$this->save_group( Settings::LAYOUT, 'projectpress_layout', 'projectpress_save_layout_settings', array( $this, 'sanitize_layout' ) );
	}

	public function access_save(): void {
		$this->save_group( Settings::ACCESS, 'projectpress_access', 'projectpress_save_access_settings', array( $this, 'sanitize_access' ) );
	}

	public function notifications_save(): void {
		$this->save_group( Settings::NOTIFICATIONS, 'projectpress_notifications', 'projectpress_save_notifications_settings', array( $this, 'sanitize_notifications' ) );
	}

	public function activity_save(): void {
		$this->save_group( Settings::ACTIVITY, 'projectpress_activity', 'projectpress_save_activity_settings', array( $this, 'sanitize_activity' ) );
	}

	private function save_group( string $group, string $field, string $nonce, callable $sanitize ): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to change ProjectPress settings.', 'projectpress' ) );
		}
		check_admin_referer( $nonce );
		$input     = isset( $_POST[ $field ] ) && is_array( $_POST[ $field ] ) ? wp_unslash( $_POST[ $field ] ) : array();
		$sanitized = call_user_func( $sanitize, $input );
		$result    = Settings::set_group( $group, $sanitized );
		$this->redirect_after_save( $group, $result, $result ? __( 'ProjectPress settings saved successfully.', 'projectpress' ) : __( 'Failed to save ProjectPress settings.', 'projectpress' ) );
	}

	public function sanitize_general( $input ): array {
		$input = is_array( $input ) ? $input : array();
		return array(
			'product_name'        => sanitize_text_field( $input['product_name'] ?? 'ProjectPress' ),
			'product_description' => sanitize_textarea_field( $input['product_description'] ?? '' ),
			'items_per_page'      => max( 1, min( 100, absint( $input['items_per_page'] ?? 20 ) ) ),
		);
	}

	public function sanitize_projects( $input ): array {
		$input = is_array( $input ) ? $input : array();
		$keys = array( 'default_status', 'default_priority', 'default_visibility', 'default_task_status', 'default_task_priority', 'default_milestone_status', 'default_requirement_status', 'default_feature_status', 'default_story_status' );
		$output = array();
		foreach ( $keys as $key ) {
			$output[ $key ] = sanitize_key( (string) ( $input[ $key ] ?? '' ) );
		}
		return $output;
	}

	public function sanitize_layout( $input ): array {
		$input = is_array( $input ) ? $input : array();
		$current = Settings::get_group( Settings::LAYOUT, array() );
		return array_merge( $current, $input );
	}

	public function sanitize_access( $input ): array {
		return is_array( $input ) ? array_map( 'sanitize_text_field', $input ) : array();
	}

	public function sanitize_notifications( $input ): array {
		$input = is_array( $input ) ? $input : array();
		$output = array();
		foreach ( array( 'enabled', 'assignment_notifications', 'due_date_notifications', 'milestone_notifications' ) as $key ) {
			$output[ $key ] = ! empty( $input[ $key ] );
		}
		return $output;
	}

	public function sanitize_activity( $input ): array {
		return array( 'enabled' => ! empty( is_array( $input ) ? $input['enabled'] ?? false : false ) );
	}

	private function redirect_after_save( string $tab, bool $success, string $message ): void {
		wp_safe_redirect( add_query_arg( array( 'page' => 'projectpress', 'group' => 'settings', 'tab' => $tab, $success ? 'settings_saved' : 'settings_failed' => '1', 'settings_message' => rawurlencode( $message ) ), admin_url( 'admin.php' ) ) );
		exit;
	}
}
