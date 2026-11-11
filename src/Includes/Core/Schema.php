<?php
/**
 * Schema class for managing core and extension database schema definitions.
 *
 * @package ProjectPress\Includes\Core
 * @since 1.0.0
 */
namespace ProjectPress\Includes\Core;

use ProjectPress\Includes\Core\WP\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Schema {
    /**
     * Register all core database tables.
     *
     * @return void
     */
    public static function register_tables(): void {
        self::settings_table();
        self::analytics_table();
        self::logs_table();
        self::projects_table();
        self::workspaces_table();
        self::project_relationships_table();
        self::activity_table();
    }
    /**
     * Register the settings table schema.
     *
     * @return string
     */
    public static function settings_table(): string {
        Database::register_core_table(
            'settings',
            static function ( string $table_name, string $charset ) {
                return "CREATE TABLE {$table_name} (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                setting_group varchar(120) NOT NULL,
                setting_value longtext DEFAULT NULL,
                autoload varchar(20) DEFAULT 'yes',
                updated_at datetime DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY setting_group (setting_group)
            ) {$charset};";
            }
        );

        return 'settings';
    }
    /**
     * Register the analytics table schema.
     *
     * @return string
     */
    public static function analytics_table(): string {
        Database::register_core_table(
            'analytics',
            static function ( string $table_name, string $charset ) {
                return "CREATE TABLE {$table_name} (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                event_key varchar(128) NOT NULL,
                event_value longtext DEFAULT NULL,
                user_id bigint(20) unsigned DEFAULT NULL,
                event_source varchar(64) DEFAULT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                KEY event_key (event_key),
                KEY created_at (created_at),
                KEY user_id (user_id)
            ) {$charset};";
            }
        );

        return 'analytics';
    }
    /**
     * Register the logs table schema.
     *
     * @return string
     */
    public static function logs_table(): string {
        Database::register_core_table(
            'logs',
            static function ( string $table_name, string $charset ) {
                return "CREATE TABLE {$table_name} (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                log_level varchar(32) NOT NULL,
                message longtext NOT NULL,
                context longtext DEFAULT NULL,
                source varchar(64) DEFAULT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                KEY log_level (log_level),
                KEY source (source),
                KEY created_at (created_at)
            ) {$charset};";
            }
        );

        return 'logs';
    }

    public static function projects_table(): string {
        Database::register_core_table(
            'projects',
            static function ( string $table_name, string $charset ) {
                return "CREATE TABLE {$table_name} (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    project_name varchar(191) NOT NULL,
                    project_slug varchar(191) NOT NULL,
                    project_key varchar(32) NOT NULL,
                    project_description longtext DEFAULT NULL,
                    parent_id bigint(20) unsigned DEFAULT NULL,
                    owner_id bigint(20) unsigned DEFAULT NULL,
                    type_id bigint(20) unsigned DEFAULT NULL,
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
                    KEY type_id (type_id),
                    KEY status (status),
                    KEY priority (priority),
                    KEY visibility (visibility),
                    KEY start_date (start_date),
                    KEY target_end_date (target_end_date),
                    KEY created_by (created_by),
                    KEY updated_at (updated_at)
                ) {$charset};";
            }
        );

        return 'projects';
    }

    public static function workspaces_table(): string {
        Database::register_core_table(
            'workspaces',
            static function ( string $table_name, string $charset ) {
                return "CREATE TABLE {$table_name} (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    workspace_name varchar(120) NOT NULL,
                    workspace_slug varchar(120) NOT NULL,
                    workspace_description longtext DEFAULT NULL,
                    created_by bigint(20) unsigned DEFAULT NULL,
                    created_at datetime DEFAULT CURRENT_TIMESTAMP,
                    updated_at datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY  (id),
                    UNIQUE KEY workspace_slug (workspace_slug),
                    KEY created_by (created_by)
                ) {$charset};";
            }
        );

        return 'workspaces';
    }

    public static function project_relationships_table(): string {
        Database::register_core_table(
            'project_relationships',
            static function ( string $table_name, string $charset ) {
                return "CREATE TABLE {$table_name} (
                    id bigint(20) unsigned NOT NULL AUTO_INCREMENT,

                    source_project_id bigint(20) unsigned NOT NULL,
                    target_project_id bigint(20) unsigned NOT NULL,

                    relationship varchar(32) NOT NULL,

                    created_by bigint(20) unsigned DEFAULT NULL,
                    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,

                    PRIMARY KEY  (id),

                    UNIQUE KEY project_relationship (
                        source_project_id,
                        target_project_id,
                        relationship
                    ),

                    KEY source_project_id (source_project_id),
                    KEY target_project_id (target_project_id),
                    KEY relationship (relationship)
                ) {$charset};";
            }
        );

        return 'project_relationships';
    }

    public static function activity_table(): string {
        Database::register_core_table(
            'activity',
            static function ( string $table_name, string $charset ) {
                return "CREATE TABLE {$table_name} (
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

                    KEY project_id (project_id),
                    KEY project_created (project_id, created_at),
                    KEY object_lookup (object_type, object_id),
                    KEY actor_id (actor_id),
                    KEY action (action)
                ) {$charset};";
            }
        );

        return 'activity';
    }
}


