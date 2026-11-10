<?php

namespace ProjectPress\Includes\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Capabilities {
	/**
	 * Capability definitions contributed by ProjectPress extensions.
	 *
	 * @var array<string, array{group: string, label: string, description: string}>
	 */
	private static array $extensions = array();

	/**
	 * Return the core and registered extension capability definitions.
	 *
	 * @return array<string, array{group: string, label: string, description: string}>
	 */
	public static function definitions(): array {
		return array_merge(
			array(
				'projectpress_admin_view'                => array(
					'group'       => 'ProjectPress Licence',
					'label'       => __( 'View Licence Administration', 'projectpress' ),
					'description' => __( 'Allows access to the ProjectPress dashboard and admin pages.', 'projectpress' ),
				),
				'projectpress_dashboard_view'            => array(
					'group'       => 'ProjectPress Licence',
					'label'       => __( 'View Licence Dashboard', 'projectpress' ),
					'description' => __( 'Allows viewing the ProjectPress dashboard and summary status.', 'projectpress' ),
				),
				'projectpress_settings_general_view'     => array(
					'group'       => 'ProjectPress Settings',
					'label'       => __( 'View Licence Settings', 'projectpress' ),
					'description' => __( 'Allows viewing the general licence management settings.', 'projectpress' ),
				),
				'projectpress_settings_general_edit'     => array(
					'group'       => 'ProjectPress Settings',
					'label'       => __( 'Edit Licence Settings', 'projectpress' ),
					'description' => __( 'Allows editing the licence management settings.', 'projectpress' ),
				),
				'projectpress_settings_layout_view'      => array(
					'group'       => 'ProjectPress Settings',
					'label'       => __( 'View Layout Settings', 'projectpress' ),
					'description' => __( 'Allows viewing ProjectPress layout and presentation settings.', 'projectpress' ),
				),
				'projectpress_settings_layout_edit'      => array(
					'group'       => 'ProjectPress Settings',
					'label'       => __( 'Edit Layout Settings', 'projectpress' ),
					'description' => __( 'Allows changing ProjectPress layout and presentation settings.', 'projectpress' ),
				),
				'projectpress_settings_access_view'      => array(
					'group'       => 'ProjectPress Settings',
					'label'       => __( 'View Access Controls', 'projectpress' ),
					'description' => __( 'Allows viewing who can do what inside ProjectPress.', 'projectpress' ),
				),
				'projectpress_settings_access_edit'      => array(
					'group'       => 'ProjectPress Settings',
					'label'       => __( 'Edit Access Controls', 'projectpress' ),
					'description' => __( 'Allows changing licence access roles and permission boundaries.', 'projectpress' ),
				),
				'projectpress_settings_security_view'    => array(
					'group'       => 'ProjectPress Settings',
					'label'       => __( 'View Security Settings', 'projectpress' ),
					'description' => __( 'Allows viewing security and export protection settings.', 'projectpress' ),
				),
				'projectpress_settings_security_edit'    => array(
					'group'       => 'ProjectPress Settings',
					'label'       => __( 'Edit Security Settings', 'projectpress' ),
					'description' => __( 'Allows editing export passwords, encryption controls, and security flags.', 'projectpress' ),
				),
				'projectpress_tools_general'              => array(
					'group'       => 'ProjectPress Tools',
					'label'       => __( 'View General Tools', 'projectpress' ),
					'description' => __( 'Allows viewing general tools and settings in ProjectPress.', 'projectpress' ),
				),
				'projectpress_tools_import'              => array(
					'group'       => 'ProjectPress Tools',
					'label'       => __( 'Import Licence Data', 'projectpress' ),
					'description' => __( 'Allows importing licence exports into the system securely.', 'projectpress' ),
				),
				'projectpress_tools_export'              => array(
					'group'       => 'ProjectPress Tools',
					'label'       => __( 'Export Licence Data', 'projectpress' ),
					'description' => __( 'Allows exporting licence records using encryption and a password.', 'projectpress' ),
				),
				'projectpress_tools_debug'               => array(
					'group'       => 'ProjectPress Tools',
					'label'       => __( 'View Debug Tools', 'projectpress' ),
					'description' => __( 'Allows using ProjectPress debug and diagnostics tools.', 'projectpress' ),
				),
				'projectpress_tools_reset'               => array(
					'group'       => 'ProjectPress Tools',
					'label'       => __( 'Reset Licence Data', 'projectpress' ),
					'description' => __( 'Allows resetting or clearing licence records and related data.', 'projectpress' ),
				),
				'projectpress_settings_plugins_view'     => array(
					'group'       => 'ProjectPress Settings',
					'label'       => __( 'View Plugin Settings', 'projectpress' ),
					'description' => __( 'Allows viewing ProjectPress plugin settings.', 'projectpress' ),
				),
				'projectpress_settings_plugins_int_view' => array(
					'group'       => 'ProjectPress Settings',
					'label'       => __( 'View Internal Plugin Settings', 'projectpress' ),
					'description' => __( 'Allows viewing settings for internal ProjectPress plugins.', 'projectpress' ),
				),
				'projectpress_settings_plugins_int_edit' => array(
					'group'       => 'ProjectPress Settings',
					'label'       => __( 'Edit Internal Plugin Settings', 'projectpress' ),
					'description' => __( 'Allows editing settings for internal ProjectPress plugins.', 'projectpress' ),
				),
				'projectpress_settings_plugins_ext_view' => array(
					'group'       => 'ProjectPress Settings',
					'label'       => __( 'View External Plugin Settings', 'projectpress' ),
					'description' => __( 'Allows viewing settings for external ProjectPress plugins.', 'projectpress' ),
				),
				'projectpress_settings_plugins_ext_edit' => array(
					'group'       => 'ProjectPress Settings',
					'label'       => __( 'Edit External Plugin Settings', 'projectpress' ),
					'description' => __( 'Allows editing settings for external ProjectPress plugins.', 'projectpress' ),
				),
				'projectpress_manager_dashboard' => array(
					'group'       => 'Mod Management',
					'label'       => __( 'View Mod Management Dashboard', 'projectpress' ),
					'description' => __( 'Allows viewing the ProjectPress Mod Management Dashboard.', 'projectpress' ),
				),
				'projectpress_manager_mods_view' => array(
					'group'       => 'Mod Management',
					'label'       => __( 'Manage Mods', 'projectpress' ),
					'description' => __( 'Allows viewing mods within Mod Management.', 'projectpress' ),
				),
				'projectpress_manager_mods_edit' => array(
					'group'       => 'Mod Management',
					'label'       => __( 'Edit Mods', 'projectpress' ),
					'description' => __( 'Allows editing mods within Mod Management.', 'projectpress' ),
				),
				'projectpress_manager_mods_edit_others' => array(
					'group'       => 'Mod Management',
					'label'       => __( 'Edit Other Mods', 'projectpress' ),
					'description' => __( 'Allows editing mods created by other users within Mod Management.', 'projectpress' ),
				),
				'projectpress_manager_groups_view' => array(
					'group'       => 'Mod Management',
					'label'       => __( 'Manage Groups', 'projectpress' ),
					'description' => __( 'Allows viewing user groups within Mod Management.', 'projectpress' ),
				),
				'projectpress_manager_groups_edit' => array(
					'group'       => 'Mod Management',
					'label'       => __( 'Edit Groups', 'projectpress' ),
					'description' => __( 'Allows editing user groups within Mod Management.', 'projectpress' ),
				),
				'projectpress_manager_tags_view' => array(
					'group'       => 'Mod Management',
					'label'       => __( 'View Tags', 'projectpress' ),
					'description' => __( 'Allows viewing tags within Mod Management.', 'projectpress' ),
				),
				'projectpress_manager_tags_edit' => array(
					'group'       => 'Mod Management',
					'label'       => __( 'Edit Tags', 'projectpress' ),
					'description' => __( 'Allows editing tags within Mod Management.', 'projectpress' ),
				),
			),
			self::$extensions
		);
	}

	/**
	 * Register definitions contributed by a plugin and install any missing caps.
	 *
	 * @param array<string, array{group: string, label: string, description: string}> $definitions Definitions to add.
	 * @return void
	 */
	public static function extend( array $definitions ): void {
		self::$extensions = array_merge( self::$extensions, $definitions );
		self::install();
	}

	/**
	 * Install missing capabilities without removing administrator customizations.
	 *
	 * @return void
	 */
	public static function install(): void {
		$administrator = get_role( 'administrator' );
		if ( ! $administrator ) {
			return;
		}

		foreach ( array_keys( self::definitions() ) as $capability ) {
			if ( ! $administrator->has_cap( $capability ) ) {
				$administrator->add_cap( $capability );
			}
		}
	}
}







