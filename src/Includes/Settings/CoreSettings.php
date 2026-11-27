<?php
/**
 * Canonical ProjectPress core settings schema.
 *
 * @package ProjectPress
 * @subpackage Includes\Settings
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Defines core settings groups and defaults.
 *
 * @since 1.0.0
 */
final class CoreSettings {
	/**
	 * Return canonical core settings defaults.
	 *
	 * @return array<string, array<string, mixed>>
	 * @since 1.0.0
	 */
	public static function defaults(): array {
		return array(
			Settings::GENERAL => array(
				'product_name'        => 'ProjectPress',
				'product_description' => __( 'Project management powered by ProjectPress.', 'projectpress' ),
				'items_per_page'      => 20,
			),
			Settings::PROJECTS => array(
				'default_status'             => 'planning',
				'default_priority'           => 'normal',
				'default_visibility'         => 'private',
				'default_task_status'        => 'todo',
				'default_task_priority'      => 'normal',
				'default_milestone_status'   => 'planned',
				'default_requirement_status' => 'draft',
				'default_feature_status'     => 'planned',
				'default_story_status'       => 'backlog',
			),
			Settings::LAYOUT => array(
				'workspace_compact_header' => false,
				'workspace_show_activity'  => true,
				'dashboard_show_summary'   => true,
			),
			Settings::ACCESS => array(),
			Settings::NOTIFICATIONS => array(
				'enabled'                 => true,
				'assignment_notifications'=> true,
				'due_date_notifications'  => true,
				'milestone_notifications' => true,
			),
			Settings::ACTIVITY => array(
				'enabled' => true,
			),
			Settings::TOOLS => array(
				'debug_logging'   => false,
				'console_logging' => false,
			),
		);
	}

	/**
	 * Register all core groups with the settings manager.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register(): void {
		foreach ( self::defaults() as $group => $defaults ) {
			Settings::register_group( $group, $defaults );
		}
	}
}
