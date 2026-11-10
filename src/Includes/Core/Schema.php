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
        self::groups_table();
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
                project_name varchar(120) NOT NULL,
                project_slug varchar(120) NOT NULL,
                project_key varchar(120) NOT NULL,
                project_description longtext DEFAULT NULL,
                project_parent bigint(20) DEFAULT NULL,
                project_team bigint(20) DEFAULT NULL,
                project_type bigint(20) DEFAULT NULL,
                project_owner bigint(20) DEFAULT NULL,
                project_start_date datetime DEFAULT NULL,
                project_target_end_date datetime DEFAULT NULL,
                project_actual_end_date datetime DEFAULT NULL,
                project_status varchar(32) DEFAULT NULL,
                project_priority varchar(32) DEFAULT NULL,
                project_visibility varchar(32) DEFAULT NULL,
                project_members longtext DEFAULT NULL,
                project_fields longtext DEFAULT NULL,
                created_by bigint(20) unsigned DEFAULT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY  (id),
                UNIQUE KEY project_key (project_key),
                UNIQUE KEY project_slug (project_slug),
                KEY created_by (created_by)
            ) {$charset};";
            }
        );

        return 'projects';
    }

}

