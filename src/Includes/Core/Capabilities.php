<?php
/**
 * ProjectPress capability registry.
 *
 * @package ProjectPress\Includes\Core
 */
namespace ProjectPress\Includes\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Capabilities {
	/** @var array<string, array{group: string, label: string, description: string}> */
	private static array $extensions = array();

	/** Return core and extension capability definitions. */
	public static function definitions(): array {
		$definitions = array(
			'projectpress_admin_view' => self::definition( 'ProjectPress Administration', 'View ProjectPress Administration', 'Allows access to ProjectPress administration pages.' ),
			'projectpress_dashboard_view' => self::definition( 'ProjectPress Administration', 'View ProjectPress Dashboard', 'Allows viewing the ProjectPress dashboard.' ),

			'projectpress_projects_view' => self::definition( 'Project Management', 'View Projects', 'Allows viewing projects available to the user.' ),
			'projectpress_projects_create' => self::definition( 'Project Management', 'Create Projects', 'Allows creating ProjectPress projects.' ),
			'projectpress_projects_edit' => self::definition( 'Project Management', 'Edit Projects', 'Allows editing ProjectPress projects permitted by project access rules.' ),
			'projectpress_projects_edit_others' => self::definition( 'Project Management', 'Edit Other Projects', 'Allows editing projects owned by other users where project access rules permit.' ),
			'projectpress_projects_delete' => self::definition( 'Project Management', 'Delete Projects', 'Allows permanently deleting projects where project access rules permit.' ),
			'projectpress_projects_taxonomies' => self::definition( 'Project Management', 'Manage Project Classifications', 'Allows managing project types, categories, tags and labels.' ),
			'projectpress_teams_view' => self::definition( 'Project Teams', 'View Teams', 'Allows viewing ProjectPress teams.' ),
			'projectpress_teams_manage' => self::definition( 'Project Teams', 'Manage Teams', 'Allows creating and managing ProjectPress teams, membership and project assignments.' ),
			'projectpress_fields_view' => self::definition( 'Project Fields', 'View Global Fields', 'Allows viewing global ProjectPress field definitions.' ),
			'projectpress_fields_manage' => self::definition( 'Project Fields', 'Manage Global Fields', 'Allows creating and managing global ProjectPress field definitions.' ),

			'projectpress_settings_general_view' => self::definition( 'ProjectPress Settings', 'View General Settings', 'Allows viewing general ProjectPress settings.' ),
			'projectpress_settings_general_edit' => self::definition( 'ProjectPress Settings', 'Edit General Settings', 'Allows editing general ProjectPress settings.' ),
			'projectpress_settings_layout_view' => self::definition( 'ProjectPress Settings', 'View Layout Settings', 'Allows viewing ProjectPress layout settings.' ),
			'projectpress_settings_layout_edit' => self::definition( 'ProjectPress Settings', 'Edit Layout Settings', 'Allows editing ProjectPress layout settings.' ),
			'projectpress_settings_access_view' => self::definition( 'ProjectPress Settings', 'View Access Controls', 'Allows viewing ProjectPress access controls.' ),
			'projectpress_settings_access_edit' => self::definition( 'ProjectPress Settings', 'Edit Access Controls', 'Allows editing ProjectPress access controls.' ),
			'projectpress_settings_security_view' => self::definition( 'ProjectPress Settings', 'View Security Settings', 'Allows viewing ProjectPress security settings.' ),
			'projectpress_settings_security_edit' => self::definition( 'ProjectPress Settings', 'Edit Security Settings', 'Allows editing ProjectPress security settings.' ),
			'projectpress_settings_plugins_view' => self::definition( 'ProjectPress Settings', 'View Plugin Settings', 'Allows viewing ProjectPress plugin settings.' ),
			'projectpress_settings_plugins_int_view' => self::definition( 'ProjectPress Settings', 'View Internal Plugin Settings', 'Allows viewing internal plugin settings.' ),
			'projectpress_settings_plugins_int_edit' => self::definition( 'ProjectPress Settings', 'Edit Internal Plugin Settings', 'Allows editing internal plugin settings.' ),
			'projectpress_settings_plugins_ext_view' => self::definition( 'ProjectPress Settings', 'View External Plugin Settings', 'Allows viewing external plugin settings.' ),
			'projectpress_settings_plugins_ext_edit' => self::definition( 'ProjectPress Settings', 'Edit External Plugin Settings', 'Allows editing external plugin settings.' ),

			'projectpress_tools_general' => self::definition( 'ProjectPress Tools', 'View General Tools', 'Allows viewing general ProjectPress tools.' ),
			'projectpress_tools_import' => self::definition( 'ProjectPress Tools', 'Import ProjectPress Data', 'Allows importing ProjectPress data.' ),
			'projectpress_tools_export' => self::definition( 'ProjectPress Tools', 'Export ProjectPress Data', 'Allows exporting ProjectPress data.' ),
			'projectpress_tools_debug' => self::definition( 'ProjectPress Tools', 'View Debug Tools', 'Allows using ProjectPress diagnostics.' ),
			'projectpress_tools_reset' => self::definition( 'ProjectPress Tools', 'Reset ProjectPress Data', 'Allows resetting ProjectPress data.' ),

			'projectpress_manager_dashboard' => self::definition( 'Legacy Mod Management', 'View Mod Management Dashboard', 'Allows viewing the legacy Mod Management dashboard.' ),
			'projectpress_manager_mods_view' => self::definition( 'Legacy Mod Management', 'View Mods', 'Allows viewing mods within legacy Mod Management.' ),
			'projectpress_manager_mods_edit' => self::definition( 'Legacy Mod Management', 'Edit Mods', 'Allows editing mods within legacy Mod Management.' ),
			'projectpress_manager_mods_edit_others' => self::definition( 'Legacy Mod Management', 'Edit Other Mods', 'Allows editing mods created by other users.' ),
			'projectpress_manager_groups_view' => self::definition( 'Legacy Mod Management', 'View Groups', 'Allows viewing legacy Mod Management groups.' ),
			'projectpress_manager_groups_edit' => self::definition( 'Legacy Mod Management', 'Edit Groups', 'Allows editing legacy Mod Management groups.' ),
			'projectpress_manager_tags_view' => self::definition( 'Legacy Mod Management', 'View Tags', 'Allows viewing legacy Mod Management tags.' ),
			'projectpress_manager_tags_edit' => self::definition( 'Legacy Mod Management', 'Edit Tags', 'Allows editing legacy Mod Management tags.' ),
		);

		return array_merge( $definitions, self::$extensions );
	}

	private static function definition( string $group, string $label, string $description ): array {
		return array(
			'group' => $group,
			'label' => __( $label, 'projectpress' ),
			'description' => __( $description, 'projectpress' ),
		);
	}

	public static function extend( array $definitions ): void {
		self::$extensions = array_merge( self::$extensions, $definitions );
		self::install();
	}

	public static function install(): void {
		$administrator = get_role( 'administrator' );
		if ( ! $administrator ) { return; }
		foreach ( array_keys( self::definitions() ) as $capability ) {
			if ( ! $administrator->has_cap( $capability ) ) { $administrator->add_cap( $capability ); }
		}
	}
}
