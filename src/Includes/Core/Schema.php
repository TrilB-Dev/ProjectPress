<?php
/**
 * Core ProjectPress database schema definitions.
 *
 * @package ProjectPress\Includes\Core
 */
namespace ProjectPress\Includes\Core;

use ProjectPress\Includes\Core\WP\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Schema {
	/** Register all core database tables. */
	public static function register_tables(): void {
		self::register( 'settings', self::settings_schema() );
		self::register( 'analytics', self::analytics_schema() );
		self::register( 'logs', self::logs_schema() );
		self::register( 'projects', self::projects_schema() );
		self::register( 'project_term_relationships', self::project_term_relationships_schema() );
		self::register( 'teams', self::teams_schema() );
		self::register( 'team_members', self::team_members_schema() );
		self::register( 'project_teams', self::project_teams_schema() );
		self::register( 'project_fields', self::project_fields_schema() );
		self::register( 'project_field_values', self::project_field_values_schema() );
		self::register( 'workspaces', self::workspaces_schema() );
		self::register( 'project_relationships', self::project_relationships_schema() );
		self::register( 'activity', self::activity_schema() );
	}

	private static function register( string $name, string $columns ): void {
		Database::register_core_table(
			$name,
			static function ( string $table_name, string $charset ) use ( $columns ): string {
				return "CREATE TABLE {$table_name} (\n{$columns}\n) {$charset};";
			}
		);
	}

	private static function settings_schema(): string { return <<<'SQL'
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	setting_group varchar(120) NOT NULL,
	setting_value longtext DEFAULT NULL,
	autoload varchar(20) NOT NULL DEFAULT 'yes',
	updated_at datetime DEFAULT NULL,
	PRIMARY KEY  (id),
	UNIQUE KEY setting_group (setting_group)
SQL; }

	private static function analytics_schema(): string { return <<<'SQL'
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	event_key varchar(128) NOT NULL,
	event_value longtext DEFAULT NULL,
	user_id bigint(20) unsigned DEFAULT NULL,
	event_source varchar(64) DEFAULT NULL,
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY  (id),
	KEY event_key (event_key),
	KEY created_at (created_at),
	KEY user_id (user_id)
SQL; }

	private static function logs_schema(): string { return <<<'SQL'
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	log_level varchar(32) NOT NULL,
	message longtext NOT NULL,
	context longtext DEFAULT NULL,
	source varchar(64) DEFAULT NULL,
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY  (id),
	KEY log_level (log_level),
	KEY source (source),
	KEY created_at (created_at)
SQL; }

	private static function projects_schema(): string { return <<<'SQL'
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	project_name varchar(191) NOT NULL,
	project_slug varchar(191) NOT NULL,
	project_key varchar(32) NOT NULL,
	project_description longtext DEFAULT NULL,
	parent_id bigint(20) unsigned DEFAULT NULL,
	owner_id bigint(20) unsigned DEFAULT NULL,
	status varchar(32) NOT NULL DEFAULT 'planning',
	priority varchar(32) NOT NULL DEFAULT 'normal',
	visibility varchar(32) NOT NULL DEFAULT 'private',
	start_date datetime DEFAULT NULL,
	target_end_date datetime DEFAULT NULL,
	actual_end_date datetime DEFAULT NULL,
	created_by bigint(20) unsigned DEFAULT NULL,
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_by bigint(20) unsigned DEFAULT NULL,
	updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY  (id),
	UNIQUE KEY project_key (project_key),
	UNIQUE KEY project_slug (project_slug),
	KEY parent_id (parent_id),
	KEY owner_id (owner_id),
	KEY status (status),
	KEY priority (priority),
	KEY visibility (visibility),
	KEY updated_at (updated_at)
SQL; }

	private static function project_term_relationships_schema(): string { return <<<'SQL'
	project_id bigint(20) unsigned NOT NULL,
	term_id bigint(20) unsigned NOT NULL,
	taxonomy varchar(32) NOT NULL,
	assigned_by bigint(20) unsigned DEFAULT NULL,
	assigned_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY  (project_id,term_id,taxonomy),
	KEY term_taxonomy (term_id,taxonomy),
	KEY taxonomy (taxonomy)
SQL; }

	private static function teams_schema(): string { return <<<'SQL'
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	team_name varchar(191) NOT NULL,
	team_slug varchar(191) NOT NULL,
	team_description longtext DEFAULT NULL,
	owner_id bigint(20) unsigned DEFAULT NULL,
	status varchar(32) NOT NULL DEFAULT 'active',
	created_by bigint(20) unsigned DEFAULT NULL,
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_by bigint(20) unsigned DEFAULT NULL,
	updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY  (id),
	UNIQUE KEY team_slug (team_slug),
	KEY owner_id (owner_id),
	KEY status (status)
SQL; }

	private static function team_members_schema(): string { return <<<'SQL'
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	team_id bigint(20) unsigned NOT NULL,
	user_id bigint(20) unsigned NOT NULL,
	member_role varchar(32) NOT NULL DEFAULT 'member',
	member_status varchar(32) NOT NULL DEFAULT 'active',
	added_by bigint(20) unsigned DEFAULT NULL,
	joined_at datetime DEFAULT NULL,
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY  (id),
	UNIQUE KEY team_user (team_id,user_id),
	KEY user_id (user_id),
	KEY member_status (member_status)
SQL; }

	private static function project_teams_schema(): string { return <<<'SQL'
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	project_id bigint(20) unsigned NOT NULL,
	team_id bigint(20) unsigned NOT NULL,
	team_role varchar(32) NOT NULL DEFAULT 'contributor',
	assigned_by bigint(20) unsigned DEFAULT NULL,
	assigned_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY  (id),
	UNIQUE KEY project_team (project_id,team_id),
	KEY team_id (team_id)
SQL; }

	private static function project_fields_schema(): string { return <<<'SQL'
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	field_key varchar(120) NOT NULL,
	field_name varchar(191) NOT NULL,
	field_description longtext DEFAULT NULL,
	field_type varchar(32) NOT NULL,
	configuration longtext DEFAULT NULL,
	is_required tinyint(1) NOT NULL DEFAULT 0,
	is_enabled tinyint(1) NOT NULL DEFAULT 1,
	sort_order int(11) NOT NULL DEFAULT 0,
	created_by bigint(20) unsigned DEFAULT NULL,
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_by bigint(20) unsigned DEFAULT NULL,
	updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY  (id),
	UNIQUE KEY field_key (field_key),
	KEY field_type (field_type),
	KEY enabled_order (is_enabled,sort_order)
SQL; }

	private static function project_field_values_schema(): string { return <<<'SQL'
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	project_id bigint(20) unsigned NOT NULL,
	field_id bigint(20) unsigned NOT NULL,
	value_text varchar(191) DEFAULT NULL,
	value_longtext longtext DEFAULT NULL,
	value_number decimal(20,6) DEFAULT NULL,
	value_date datetime DEFAULT NULL,
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_by bigint(20) unsigned DEFAULT NULL,
	updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY  (id),
	UNIQUE KEY project_field (project_id,field_id),
	KEY field_id (field_id),
	KEY value_date (value_date)
SQL; }

	private static function workspaces_schema(): string { return <<<'SQL'
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	workspace_name varchar(120) NOT NULL,
	workspace_slug varchar(120) NOT NULL,
	workspace_description longtext DEFAULT NULL,
	created_by bigint(20) unsigned DEFAULT NULL,
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY  (id),
	UNIQUE KEY workspace_slug (workspace_slug)
SQL; }

	private static function project_relationships_schema(): string { return <<<'SQL'
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	source_project_id bigint(20) unsigned NOT NULL,
	target_project_id bigint(20) unsigned NOT NULL,
	relationship varchar(32) NOT NULL,
	created_by bigint(20) unsigned DEFAULT NULL,
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY  (id),
	UNIQUE KEY project_relationship (source_project_id,target_project_id,relationship),
	KEY target_project_id (target_project_id),
	KEY relationship (relationship)
SQL; }

	private static function activity_schema(): string { return <<<'SQL'
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	project_id bigint(20) unsigned NOT NULL,
	object_type varchar(50) NOT NULL,
	object_id bigint(20) unsigned DEFAULT NULL,
	action varchar(80) NOT NULL,
	actor_id bigint(20) unsigned DEFAULT NULL,
	activity_summary text DEFAULT NULL,
	activity_data longtext DEFAULT NULL,
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY  (id),
	KEY project_created (project_id,created_at),
	KEY object_lookup (object_type,object_id),
	KEY actor_id (actor_id),
	KEY action (action)
SQL; }
}
