<?php
/**
 * Persistence for ProjectPress teams and project/team relationships.
 *
 * @package ProjectPress\Includes\Project\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Core;

use ProjectPress\Includes\Functions\Helpers\DBHelper;

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Reads and writes teams, memberships, and project assignments.
 *
 * @since 1.0.0
 */
final class TeamRepository {
    /**
     * Get all teams.
     * @return array List of all teams.
     * @since 1.0.0
     */
    public function all(): array {
        return DBHelper::get_all("teams", [], "*", "team_name ASC");
    }

    /**
     * Find a team by its ID.
     *
     * @param int $team_id Team ID.
     * @return array|null Team data or null if not found.
     * @since 1.0.0
     */
    public function find(int $team_id): ?array {
        return DBHelper::get_by_id("teams", absint($team_id));
    }

    /**
     * Create a new team.
     *
     * @param array $data Team data.
     * @return int|false Inserted team ID or false on failure.
     * @since 1.0.0
     */
    public function create(array $data) {
        return DBHelper::insert("teams", $data, ["%s", "%s", "%s", "%d", "%s", "%d", "%s", "%d", "%s"]);
    }

    /**
     * Update an existing team.
     *
     * @param int   $team_id Team ID.
     * @param array $data    Team data.
     * @return bool True on success, false on failure.
     * @since 1.0.0
     */
    public function update(int $team_id, array $data): bool {
        return DBHelper::update("teams", $data, ["id" => absint($team_id)]);
    }

    /**
     * Get all members of a team.
     *
     * @param int $team_id Team ID.
     * @return array List of team members.
     * @since 1.0.0
     */
    public function members(int $team_id): array {
        return DBHelper::get_all("team_members", ["team_id" => absint($team_id)], "*", "created_at ASC");
    }

    /**
     * Add a member to a team.
     *
     * @param int    $team_id Team ID.
     * @param int    $user_id User ID.
     * @param string $role    Member role.
     * @param string $status  Member status.
     * @return bool True on success, false on failure.
     * @since 1.0.0
     */
    public function add_member(int $team_id, int $user_id, string $role, string $status = "active"): bool {
        $existing = DBHelper::get_row("team_members", ["team_id" => $team_id, "user_id" => $user_id]);
        $data = ["member_role" => $role, "member_status" => $status, "updated_at" => current_time("mysql", true)];
        if ($existing) {
            return DBHelper::update("team_members", $data, ["id" => (int) $existing["id"]]);
        }
        $data = array_merge(
            [
                "team_id" => $team_id,
                "user_id" => $user_id,
                "added_by" => get_current_user_id(),
                "joined_at" => current_time("mysql", true),
                "created_at" => current_time("mysql", true),
            ],
            $data,
        );
        return false !== DBHelper::insert("team_members", $data);
    }

    /**
     * Remove a member from a team.
     *
     * @param int $team_id Team ID.
     * @param int $user_id User ID.
     * @return bool True on success, false on failure.
     * @since 1.0.0
     */
    public function remove_member(int $team_id, int $user_id): bool {
        return DBHelper::delete("team_members", ["team_id" => $team_id, "user_id" => $user_id]);
    }

    /**
     * Get all projects assigned to a team.
     *
     * @param int $team_id Team ID.
     * @return array List of projects.
     * @since 1.0.0
     */
    public function projects(int $team_id): array {
        return DBHelper::get_all("project_teams", ["team_id" => absint($team_id)], "*", "assigned_at ASC");
    }

    /**
     * Get all teams assigned to a project.
     *
     * @param int $project_id Project ID.
     * @return array List of teams.
     * @since 1.0.0
     */
    public function for_project(int $project_id): array {
        return DBHelper::get_all("project_teams", ["project_id" => absint($project_id)], "*", "assigned_at ASC");
    }

    /**
     * Assign a project to a team.
     *
     * @param int    $team_id    Team ID.
     * @param int    $project_id Project ID.
     * @param string $role       Team role in the project.
     * @return bool True on success, false on failure.
     * @since 1.0.0
     */
    public function assign_project(int $team_id, int $project_id, string $role): bool {
        $existing = DBHelper::get_row("project_teams", ["team_id" => $team_id, "project_id" => $project_id]);
        if ($existing) {
            return DBHelper::update("project_teams", ["team_role" => $role], ["id" => (int) $existing["id"]]);
        }
        return false !==
            DBHelper::insert("project_teams", [
                "team_id" => $team_id,
                "project_id" => $project_id,
                "team_role" => $role,
                "assigned_by" => get_current_user_id(),
                "assigned_at" => current_time("mysql", true),
            ]);
    }

    /**
     * Remove a project assignment from a team.
     *
     * @param int $team_id Team ID.
     * @param int $project_id Project ID.
     * @return bool True on success, false on failure.
     * @since 1.0.0
     */
    public function remove_project(int $team_id, int $project_id): bool {
        return DBHelper::delete("project_teams", ["team_id" => $team_id, "project_id" => $project_id]);
    }
}
