<?php
/**
 * Schema class for managing core and extension database schema definitions.
 *
 * @package ProjectPress\Includes\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Core;

use ProjectPress\Includes\Core\WP\Database;

if (!defined("ABSPATH")) {
    exit();
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
        self::project_term_relationships_table();
        self::teams_table();
        self::team_members_table();
        self::project_teams_table();
        self::project_fields_table();
        self::project_field_values_table();
        self::workspaces_table();
        self::project_relationships_table();
        self::activity_table();
        self::tasks_table();
        self::milestones_table();
        self::requirements_table();
        self::features_table();
        self::user_stories_table();
        self::risks_table();
        self::issues_table();
        self::dependencies_table();
        self::decisions_table();
        self::bugs_table();
        self::test_cases_table();
        self::sprints_table();
        self::releases_table();
        self::deployments_table();
        self::environments_table();
        self::timesheets_table();
        self::project_notifications_table();
        self::components_table();
        self::packages_table();
        self::composition_assets_table();
        self::composition_licences_table();
        self::attributions_table();
        self::provenance_table();
        self::composition_relationships_table();
        self::sboms_table();
        self::discussions_table();
        self::notes_table();
        self::files_table();
        self::project_settings_table();
    }

    /**
     * Register the settings table schema.
     *
     * @return string
     */
    public static function settings_table(): string {
        Database::register_core_table("settings", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "setting_group varchar(120) NOT NULL,\n" .
                "setting_value longtext DEFAULT NULL,\n" .
                "autoload varchar(20) NOT NULL DEFAULT 'yes',\n" .
                "updated_at datetime DEFAULT NULL,\n" .
                "PRIMARY KEY (id),\n" .
                "UNIQUE KEY setting_group (setting_group)\n" .
                ") {$charset};";
        });

        return "settings";
    }

    /**
     * Register the analytics table schema.
     *
     * @return string
     */
    public static function analytics_table(): string {
        Database::register_core_table("analytics", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "event_key varchar(128) NOT NULL,\n" .
                "event_value longtext DEFAULT NULL,\n" .
                "user_id bigint(20) unsigned DEFAULT NULL,\n" .
                "event_source varchar(64) DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY event_key (event_key),\n" .
                "KEY created_at (created_at),\n" .
                "KEY user_id (user_id)\n" .
                ") {$charset};";
        });

        return "analytics";
    }

    /**
     * Register the logs table schema.
     *
     * @return string
     */
    public static function logs_table(): string {
        Database::register_core_table("logs", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "log_level varchar(32) NOT NULL,\n" .
                "message longtext NOT NULL,\n" .
                "context longtext DEFAULT NULL,\n" .
                "source varchar(64) DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY log_level (log_level),\n" .
                "KEY source (source),\n" .
                "KEY created_at (created_at)\n" .
                ") {$charset};";
        });

        return "logs";
    }

    /**
     * Register the projects table schema.
     *
     * @return string
     */
    public static function projects_table(): string {
        Database::register_core_table("projects", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_name varchar(191) NOT NULL,\n" .
                "project_slug varchar(191) NOT NULL,\n" .
                "project_key varchar(32) NOT NULL,\n" .
                "project_description longtext DEFAULT NULL,\n" .
                "parent_id bigint(20) unsigned DEFAULT NULL,\n" .
                "owner_id bigint(20) unsigned DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'planning',\n" .
                "priority varchar(32) NOT NULL DEFAULT 'normal',\n" .
                "visibility varchar(32) NOT NULL DEFAULT 'private',\n" .
                "start_date datetime DEFAULT NULL,\n" .
                "target_end_date datetime DEFAULT NULL,\n" .
                "actual_end_date datetime DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "UNIQUE KEY project_key (project_key),\n" .
                "UNIQUE KEY project_slug (project_slug),\n" .
                "KEY parent_id (parent_id),\n" .
                "KEY owner_id (owner_id),\n" .
                "KEY status (status),\n" .
                "KEY priority (priority),\n" .
                "KEY visibility (visibility),\n" .
                "KEY updated_at (updated_at)\n" .
                ") {$charset};";
        });

        return "projects";
    }

    /**
     * Register the project_term_relationships table schema.
     *
     * @return string
     */
    public static function project_term_relationships_table(): string {
        Database::register_core_table("project_term_relationships", static function (
            string $table_name,
            string $charset,
        ) {
            return "CREATE TABLE {$table_name} (\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "term_id bigint(20) unsigned NOT NULL,\n" .
                "taxonomy varchar(32) NOT NULL,\n" .
                "assigned_by bigint(20) unsigned DEFAULT NULL,\n" .
                "assigned_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (project_id,term_id,taxonomy),\n" .
                "KEY term_taxonomy (term_id,taxonomy),\n" .
                "KEY taxonomy (taxonomy)\n" .
                ") {$charset};";
        });

        return "project_term_relationships";
    }

    /**
     * Register the teams table schema.
     *
     * @return string
     */
    public static function teams_table(): string {
        Database::register_core_table("teams", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "team_name varchar(191) NOT NULL,\n" .
                "team_slug varchar(191) NOT NULL,\n" .
                "team_description longtext DEFAULT NULL,\n" .
                "owner_id bigint(20) unsigned DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'active',\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "UNIQUE KEY team_slug (team_slug),\n" .
                "KEY owner_id (owner_id),\n" .
                "KEY status (status)\n" .
                ") {$charset};";
        });

        return "teams";
    }

    /**
     * Register the team_members table schema.
     *
     * @return string
     */
    public static function team_members_table(): string {
        Database::register_core_table("team_members", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "team_id bigint(20) unsigned NOT NULL,\n" .
                "user_id bigint(20) unsigned NOT NULL,\n" .
                "member_role varchar(32) NOT NULL DEFAULT 'member',\n" .
                "member_status varchar(32) NOT NULL DEFAULT 'active',\n" .
                "added_by bigint(20) unsigned DEFAULT NULL,\n" .
                "joined_at datetime DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "UNIQUE KEY team_user (team_id,user_id),\n" .
                "KEY user_id (user_id),\n" .
                "KEY member_status (member_status)\n" .
                ") {$charset};";
        });

        return "team_members";
    }

    /**
     * Register the project_teams table schema.
     *
     * @return string
     */
    public static function project_teams_table(): string {
        Database::register_core_table("project_teams", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "team_id bigint(20) unsigned NOT NULL,\n" .
                "team_role varchar(32) NOT NULL DEFAULT 'contributor',\n" .
                "assigned_by bigint(20) unsigned DEFAULT NULL,\n" .
                "assigned_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "UNIQUE KEY project_team (project_id,team_id),\n" .
                "KEY team_id (team_id)\n" .
                ") {$charset};";
        });

        return "project_teams";
    }

    /**
     * Register the project_fields table schema.
     *
     * @return string
     */
    public static function project_fields_table(): string {
        Database::register_core_table("project_fields", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "field_key varchar(120) NOT NULL,\n" .
                "field_name varchar(191) NOT NULL,\n" .
                "field_description longtext DEFAULT NULL,\n" .
                "field_type varchar(32) NOT NULL,\n" .
                "configuration longtext DEFAULT NULL,\n" .
                "is_required tinyint(1) NOT NULL DEFAULT 0,\n" .
                "is_enabled tinyint(1) NOT NULL DEFAULT 1,\n" .
                "sort_order int(11) NOT NULL DEFAULT 0,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "UNIQUE KEY field_key (field_key),\n" .
                "KEY field_type (field_type),\n" .
                "KEY enabled_order (is_enabled,sort_order)\n" .
                ") {$charset};";
        });

        return "project_fields";
    }

    /**
     * Register the project_field_values table schema.
     *
     * @return string
     */
    public static function project_field_values_table(): string {
        Database::register_core_table("project_field_values", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "field_id bigint(20) unsigned NOT NULL,\n" .
                "value_text varchar(191) DEFAULT NULL,\n" .
                "value_longtext longtext DEFAULT NULL,\n" .
                "value_number decimal(20,6) DEFAULT NULL,\n" .
                "value_date datetime DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "UNIQUE KEY project_field (project_id,field_id),\n" .
                "KEY field_id (field_id),\n" .
                "KEY value_date (value_date)\n" .
                ") {$charset};";
        });

        return "project_field_values";
    }

    /**
     * Register the workspaces table schema.
     *
     * @return string
     */
    public static function workspaces_table(): string {
        Database::register_core_table("workspaces", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "workspace_name varchar(120) NOT NULL,\n" .
                "workspace_slug varchar(120) NOT NULL,\n" .
                "workspace_description longtext DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "UNIQUE KEY workspace_slug (workspace_slug)\n" .
                ") {$charset};";
        });

        return "workspaces";
    }

    /**
     * Register the project_relationships table schema.
     *
     * @return string
     */
    public static function project_relationships_table(): string {
        Database::register_core_table("project_relationships", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "source_project_id bigint(20) unsigned NOT NULL,\n" .
                "target_project_id bigint(20) unsigned NOT NULL,\n" .
                "relationship varchar(32) NOT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "UNIQUE KEY project_relationship (source_project_id,target_project_id,relationship),\n" .
                "KEY target_project_id (target_project_id),\n" .
                "KEY relationship (relationship)\n" .
                ") {$charset};";
        });

        return "project_relationships";
    }

    /**
     * Register the activity table schema.
     *
     * @return string
     */
    public static function activity_table(): string {
        Database::register_core_table("activity", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "object_type varchar(50) NOT NULL,\n" .
                "object_id bigint(20) unsigned DEFAULT NULL,\n" .
                "action varchar(80) NOT NULL,\n" .
                "actor_id bigint(20) unsigned DEFAULT NULL,\n" .
                "activity_summary text DEFAULT NULL,\n" .
                "activity_data longtext DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_created (project_id,created_at),\n" .
                "KEY object_lookup (object_type,object_id),\n" .
                "KEY actor_id (actor_id),\n" .
                "KEY action (action)\n" .
                ") {$charset};";
        });

        return "activity";
    }

    /**
     * Register the tasks table schema.
     *
     * @return string
     */
    public static function tasks_table(): string {
        Database::register_core_table("tasks", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "task_title varchar(191) NOT NULL,\n" .
                "task_description longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'todo',\n" .
                "priority varchar(32) NOT NULL DEFAULT 'normal',\n" .
                "assignee_id bigint(20) unsigned DEFAULT NULL,\n" .
                "due_date datetime DEFAULT NULL,\n" .
                "sort_order int(11) NOT NULL DEFAULT 0,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_status (project_id,status),\n" .
                "KEY assignee_id (assignee_id),\n" .
                "KEY due_date (due_date)\n" .
                ") {$charset};";
        });

        return "tasks";
    }

    /**
     * Register the milestones table schema.
     *
     * @return string
     */
    public static function milestones_table(): string {
        Database::register_core_table("milestones", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "title varchar(191) NOT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'planned',\n" .
                "target_date datetime DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_status (project_id,status),\n" .
                "KEY target_date (target_date)\n" .
                ") {$charset};";
        });

        return "milestones";
    }

    /**
     * Register the requirements table schema.
     *
     * @return string
     */
    public static function requirements_table(): string {
        Database::register_core_table("requirements", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "title varchar(191) NOT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'draft',\n" .
                "priority varchar(32) NOT NULL DEFAULT 'normal',\n" .
                "sort_order int(11) NOT NULL DEFAULT 0,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_status (project_id,status),\n" .
                "KEY project_priority (project_id,priority)\n" .
                ") {$charset};";
        });

        return "requirements";
    }

    /**
     * Register the features table schema.
     *
     * @return string
     */
    public static function features_table(): string {
        Database::register_core_table("features", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "title varchar(191) NOT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'planned',\n" .
                "sort_order int(11) NOT NULL DEFAULT 0,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_status (project_id,status)\n" .
                ") {$charset};";
        });

        return "features";
    }

    /**
     * Register the user_stories table schema.
     *
     * @return string
     */
    public static function user_stories_table(): string {
        Database::register_core_table("user_stories", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "title varchar(191) NOT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'backlog',\n" .
                "points int(11) unsigned NOT NULL DEFAULT 0,\n" .
                "sort_order int(11) NOT NULL DEFAULT 0,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_status (project_id,status)\n" .
                ") {$charset};";
        });

        return "user_stories";
    }

    /**
     * Register the risks table schema.
     *
     * @return string
     */
    public static function risks_table(): string {
        Database::register_core_table("risks", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "title varchar(191) NOT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'open',\n" .
                "probability varchar(32) DEFAULT NULL,\n" .
                "impact varchar(32) DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_status (project_id,status),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "risks";
    }

    /**
     * Register the issues table schema.
     *
     * @return string
     */
    public static function issues_table(): string {
        Database::register_core_table("issues", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "title varchar(191) NOT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'open',\n" .
                "probability varchar(32) DEFAULT NULL,\n" .
                "impact varchar(32) DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_status (project_id,status),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "issues";
    }

    /**
     * Register the dependencies table schema.
     *
     * @return string
     */
    public static function dependencies_table(): string {
        Database::register_core_table("dependencies", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "title varchar(191) NOT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'open',\n" .
                "probability varchar(32) DEFAULT NULL,\n" .
                "impact varchar(32) DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_status (project_id,status),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "dependencies";
    }

    /**
     * Register the decisions table schema.
     *
     * @return string
     */
    public static function decisions_table(): string {
        Database::register_core_table("decisions", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "title varchar(191) NOT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'open',\n" .
                "probability varchar(32) DEFAULT NULL,\n" .
                "impact varchar(32) DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_status (project_id,status),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "decisions";
    }

    /**
     * Register the bugs table schema.
     *
     * @return string
     */
    public static function bugs_table(): string {
        Database::register_core_table("bugs", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "title varchar(191) NOT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'pending',\n" .
                "severity varchar(32) NOT NULL DEFAULT 'normal',\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_status (project_id,status),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "bugs";
    }

    /**
     * Register the test_cases table schema.
     *
     * @return string
     */
    public static function test_cases_table(): string {
        Database::register_core_table("test_cases", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "title varchar(191) NOT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'pending',\n" .
                "severity varchar(32) NOT NULL DEFAULT 'normal',\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_status (project_id,status),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "test_cases";
    }

    /**
     * Register the sprints table schema.
     *
     * @return string
     */
    public static function sprints_table(): string {
        Database::register_core_table("sprints", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "name varchar(191) NOT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'planned',\n" .
                "version varchar(64) DEFAULT NULL,\n" .
                "start_date datetime DEFAULT NULL,\n" .
                "end_date datetime DEFAULT NULL,\n" .
                "environment_id bigint(20) unsigned DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_status (project_id,status),\n" .
                "KEY environment_id (environment_id),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "sprints";
    }

    /**
     * Register the releases table schema.
     *
     * @return string
     */
    public static function releases_table(): string {
        Database::register_core_table("releases", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "name varchar(191) NOT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'planned',\n" .
                "version varchar(64) DEFAULT NULL,\n" .
                "start_date datetime DEFAULT NULL,\n" .
                "end_date datetime DEFAULT NULL,\n" .
                "environment_id bigint(20) unsigned DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_status (project_id,status),\n" .
                "KEY environment_id (environment_id),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "releases";
    }

    /**
     * Register the deployments table schema.
     *
     * @return string
     */
    public static function deployments_table(): string {
        Database::register_core_table("deployments", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "name varchar(191) NOT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'planned',\n" .
                "version varchar(64) DEFAULT NULL,\n" .
                "start_date datetime DEFAULT NULL,\n" .
                "end_date datetime DEFAULT NULL,\n" .
                "environment_id bigint(20) unsigned DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_status (project_id,status),\n" .
                "KEY environment_id (environment_id),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "deployments";
    }

    /**
     * Register the environments table schema.
     *
     * @return string
     */
    public static function environments_table(): string {
        Database::register_core_table("environments", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "name varchar(191) NOT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'planned',\n" .
                "version varchar(64) DEFAULT NULL,\n" .
                "start_date datetime DEFAULT NULL,\n" .
                "end_date datetime DEFAULT NULL,\n" .
                "environment_id bigint(20) unsigned DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_status (project_id,status),\n" .
                "KEY environment_id (environment_id),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "environments";
    }

    /**
     * Register the timesheets table schema.
     *
     * @return string
     */
    public static function timesheets_table(): string {
        Database::register_core_table("timesheets", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "user_id bigint(20) unsigned NOT NULL,\n" .
                "minutes int(11) unsigned NOT NULL DEFAULT 0,\n" .
                "work_date date DEFAULT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_user (project_id,user_id),\n" .
                "KEY work_date (work_date),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "timesheets";
    }

    /**
     * Register the project_notifications table schema.
     *
     * @return string
     */
    public static function project_notifications_table(): string {
        Database::register_core_table("project_notifications", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "recipient_id bigint(20) unsigned NOT NULL,\n" .
                "message longtext NOT NULL,\n" .
                "is_read tinyint(1) NOT NULL DEFAULT 0,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_recipient (project_id,recipient_id),\n" .
                "KEY unread (recipient_id,is_read),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "project_notifications";
    }

    /**
     * Register the components table schema.
     *
     * @return string
     */
    public static function components_table(): string {
        Database::register_core_table("components", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "name varchar(191) DEFAULT NULL,\n" .
                "version varchar(191) DEFAULT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "identifier varchar(191) DEFAULT NULL,\n" .
                "uri text DEFAULT NULL,\n" .
                "licence varchar(191) DEFAULT NULL,\n" .
                "source_type varchar(50) DEFAULT NULL,\n" .
                "source_id bigint(20) unsigned DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "relationship_type varchar(64) DEFAULT NULL,\n" .
                "format varchar(64) DEFAULT NULL,\n" .
                "payload longtext DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_name (project_id,name),\n" .
                "KEY source_lookup (project_id,source_type,source_id),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "components";
    }

    /**
     * Register the packages table schema.
     *
     * @return string
     */
    public static function packages_table(): string {
        Database::register_core_table("packages", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "name varchar(191) DEFAULT NULL,\n" .
                "version varchar(191) DEFAULT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "identifier varchar(191) DEFAULT NULL,\n" .
                "uri text DEFAULT NULL,\n" .
                "licence varchar(191) DEFAULT NULL,\n" .
                "source_type varchar(50) DEFAULT NULL,\n" .
                "source_id bigint(20) unsigned DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "relationship_type varchar(64) DEFAULT NULL,\n" .
                "format varchar(64) DEFAULT NULL,\n" .
                "payload longtext DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_name (project_id,name),\n" .
                "KEY source_lookup (project_id,source_type,source_id),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "packages";
    }

    /**
     * Register the composition_assets table schema.
     *
     * @return string
     */
    public static function composition_assets_table(): string {
        Database::register_core_table("composition_assets", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "name varchar(191) DEFAULT NULL,\n" .
                "version varchar(191) DEFAULT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "identifier varchar(191) DEFAULT NULL,\n" .
                "uri text DEFAULT NULL,\n" .
                "licence varchar(191) DEFAULT NULL,\n" .
                "source_type varchar(50) DEFAULT NULL,\n" .
                "source_id bigint(20) unsigned DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "relationship_type varchar(64) DEFAULT NULL,\n" .
                "format varchar(64) DEFAULT NULL,\n" .
                "payload longtext DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_name (project_id,name),\n" .
                "KEY source_lookup (project_id,source_type,source_id),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "composition_assets";
    }

    /**
     * Register the composition_licences table schema.
     *
     * @return string
     */
    public static function composition_licences_table(): string {
        Database::register_core_table("composition_licences", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "name varchar(191) DEFAULT NULL,\n" .
                "version varchar(191) DEFAULT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "identifier varchar(191) DEFAULT NULL,\n" .
                "uri text DEFAULT NULL,\n" .
                "licence varchar(191) DEFAULT NULL,\n" .
                "source_type varchar(50) DEFAULT NULL,\n" .
                "source_id bigint(20) unsigned DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "relationship_type varchar(64) DEFAULT NULL,\n" .
                "format varchar(64) DEFAULT NULL,\n" .
                "payload longtext DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_name (project_id,name),\n" .
                "KEY source_lookup (project_id,source_type,source_id),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "composition_licences";
    }

    /**
     * Register the attributions table schema.
     *
     * @return string
     */
    public static function attributions_table(): string {
        Database::register_core_table("attributions", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "name varchar(191) DEFAULT NULL,\n" .
                "version varchar(191) DEFAULT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "identifier varchar(191) DEFAULT NULL,\n" .
                "uri text DEFAULT NULL,\n" .
                "licence varchar(191) DEFAULT NULL,\n" .
                "source_type varchar(50) DEFAULT NULL,\n" .
                "source_id bigint(20) unsigned DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "relationship_type varchar(64) DEFAULT NULL,\n" .
                "format varchar(64) DEFAULT NULL,\n" .
                "payload longtext DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_name (project_id,name),\n" .
                "KEY source_lookup (project_id,source_type,source_id),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "attributions";
    }

    /**
     * Register the provenance table schema.
     *
     * @return string
     */
    public static function provenance_table(): string {
        Database::register_core_table("provenance", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "name varchar(191) DEFAULT NULL,\n" .
                "version varchar(191) DEFAULT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "identifier varchar(191) DEFAULT NULL,\n" .
                "uri text DEFAULT NULL,\n" .
                "licence varchar(191) DEFAULT NULL,\n" .
                "source_type varchar(50) DEFAULT NULL,\n" .
                "source_id bigint(20) unsigned DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "relationship_type varchar(64) DEFAULT NULL,\n" .
                "format varchar(64) DEFAULT NULL,\n" .
                "payload longtext DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_name (project_id,name),\n" .
                "KEY source_lookup (project_id,source_type,source_id),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "provenance";
    }

    /**
     * Register the composition_relationships table schema.
     *
     * @return string
     */
    public static function composition_relationships_table(): string {
        Database::register_core_table("composition_relationships", static function (
            string $table_name,
            string $charset,
        ) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "name varchar(191) DEFAULT NULL,\n" .
                "version varchar(191) DEFAULT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "identifier varchar(191) DEFAULT NULL,\n" .
                "uri text DEFAULT NULL,\n" .
                "licence varchar(191) DEFAULT NULL,\n" .
                "source_type varchar(50) DEFAULT NULL,\n" .
                "source_id bigint(20) unsigned DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "relationship_type varchar(64) DEFAULT NULL,\n" .
                "format varchar(64) DEFAULT NULL,\n" .
                "payload longtext DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_name (project_id,name),\n" .
                "KEY source_lookup (project_id,source_type,source_id),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "composition_relationships";
    }

    /**
     * Register the sboms table schema.
     *
     * @return string
     */
    public static function sboms_table(): string {
        Database::register_core_table("sboms", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "name varchar(191) DEFAULT NULL,\n" .
                "version varchar(191) DEFAULT NULL,\n" .
                "description longtext DEFAULT NULL,\n" .
                "identifier varchar(191) DEFAULT NULL,\n" .
                "uri text DEFAULT NULL,\n" .
                "licence varchar(191) DEFAULT NULL,\n" .
                "source_type varchar(50) DEFAULT NULL,\n" .
                "source_id bigint(20) unsigned DEFAULT NULL,\n" .
                "target_type varchar(50) DEFAULT NULL,\n" .
                "target_id bigint(20) unsigned DEFAULT NULL,\n" .
                "relationship_type varchar(64) DEFAULT NULL,\n" .
                "format varchar(64) DEFAULT NULL,\n" .
                "payload longtext DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_name (project_id,name),\n" .
                "KEY source_lookup (project_id,source_type,source_id),\n" .
                "KEY target_lookup (project_id,target_type,target_id)\n" .
                ") {$charset};";
        });

        return "sboms";
    }

    /**
     * Register the discussions table schema.
     *
     * @return string
     */
    public static function discussions_table(): string {
        Database::register_core_table("discussions", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "title varchar(191) NOT NULL,\n" .
                "content longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'open',\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_created (project_id,created_at),\n" .
                "KEY status (status)\n" .
                ") {$charset};";
        });

        return "discussions";
    }

    /**
     * Register the notes table schema.
     *
     * @return string
     */
    public static function notes_table(): string {
        Database::register_core_table("notes", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "title varchar(191) NOT NULL,\n" .
                "content longtext DEFAULT NULL,\n" .
                "status varchar(32) NOT NULL DEFAULT 'open',\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "KEY project_created (project_id,created_at),\n" .
                "KEY status (status)\n" .
                ") {$charset};";
        });

        return "notes";
    }

    /**
     * Register the files table schema.
     *
     * @return string
     */
    public static function files_table(): string {
        Database::register_core_table("files", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "attachment_id bigint(20) unsigned NOT NULL,\n" .
                "file_label varchar(191) DEFAULT NULL,\n" .
                "created_by bigint(20) unsigned DEFAULT NULL,\n" .
                "created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "UNIQUE KEY project_attachment (project_id,attachment_id),\n" .
                "KEY attachment_id (attachment_id)\n" .
                ") {$charset};";
        });

        return "files";
    }

    /**
     * Register the project_settings table schema.
     *
     * @return string
     */
    public static function project_settings_table(): string {
        Database::register_core_table("project_settings", static function (string $table_name, string $charset) {
            return "CREATE TABLE {$table_name} (\n" .
                "id bigint(20) unsigned NOT NULL AUTO_INCREMENT,\n" .
                "project_id bigint(20) unsigned NOT NULL,\n" .
                "setting_key varchar(120) NOT NULL,\n" .
                "setting_value longtext DEFAULT NULL,\n" .
                "updated_by bigint(20) unsigned DEFAULT NULL,\n" .
                "updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n" .
                "PRIMARY KEY (id),\n" .
                "UNIQUE KEY project_setting (project_id,setting_key)\n" .
                ") {$charset};";
        });

        return "project_settings";
    }
}
