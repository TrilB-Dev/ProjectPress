<?php
/**
 * Team validation and mutation service.
 *
 * @package ProjectPress\Includes\Project\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Core;

use ProjectPress\Includes\Functions\Helpers\PermissionHelper;
use WP_Error;

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Applies team and project-team mutation rules.
 *
 * @since 1.0.0
 */
final class TeamService {
    /**
     * Team repository instance.
     *
     * @var TeamRepository
     * @since 1.0.0
     */
    private TeamRepository $repository;
    /**
     * Project repository instance.
     *
     * @var ProjectRepository
     * @since 1.0.0
     */
    private ProjectRepository $projects;
    /**
     * Project permissions instance.
     *
     * @var ProjectPermissions
     * @since 1.0.0
     */
    private ProjectPermissions $project_permissions;

    /**
     * Initialise the team service.
     *
     * @param TeamRepository     $repository Team repository.
     * @param ProjectRepository  $projects Project repository.
     * @param ProjectPermissions $project_permissions Project permissions.
     * @since 1.0.0
     */
    public function __construct(
        TeamRepository $repository,
        ProjectRepository $projects,
        ProjectPermissions $project_permissions,
    ) {
        $this->repository = $repository;
        $this->projects = $projects;
        $this->project_permissions = $project_permissions;
    }

    /**
     * Create a new team.
     *
     * @param array $input Team data.
     * @return array|WP_Error Created team data or WP_Error on failure.
     * @since 1.0.0
     */
    public function create(array $input) {
        if (!$this->can_manage()) {
            return new WP_Error(
                "projectpress_team_forbidden",
                __("You do not have permission to manage teams.", "projectpress"),
            );
        }
        $name = sanitize_text_field($input["name"] ?? "");
        if ("" === $name) {
            return new WP_Error("projectpress_team_name_required", __("A team name is required.", "projectpress"));
        }
        $status = sanitize_key($input["status"] ?? "active");
        if (!in_array($status, ["active", "inactive"], true)) {
            return new WP_Error("projectpress_team_status_invalid", __("The team status is invalid.", "projectpress"));
        }
        $now = current_time("mysql", true);
        $id = $this->repository->create([
            "team_name" => $name,
            "team_slug" => sanitize_title($input["slug"] ?? $name),
            "team_description" => sanitize_textarea_field($input["description"] ?? ""),
            "owner_id" => absint($input["owner_id"] ?? get_current_user_id()),
            "status" => $status,
            "created_by" => get_current_user_id(),
            "created_at" => $now,
            "updated_by" => get_current_user_id(),
            "updated_at" => $now,
        ]);
        if (false === $id) {
            return new WP_Error(
                "projectpress_team_create_failed",
                __("The team could not be created.", "projectpress"),
            );
        }
        do_action("projectpress_team_created", $id);
        return $this->repository->find((int) $id);
    }

    /**
     * Add a member to a team.
     *
     * @param int $team_id Team ID.
     * @param int $user_id User ID.
     * @param string $role Member role.
     * @return true|WP_Error True on success or WP_Error on failure.
     * @since 1.0.0
     */
    public function add_member(int $team_id, int $user_id, string $role = "member") {
        if (!$this->can_manage()) {
            return new WP_Error(
                "projectpress_team_forbidden",
                __("You do not have permission to manage teams.", "projectpress"),
            );
        }
        if (!$this->repository->find($team_id)) {
            return new WP_Error("projectpress_team_not_found", __("Team not found.", "projectpress"));
        }
        if (!get_user_by("id", $user_id)) {
            return new WP_Error(
                "projectpress_team_user_not_found",
                __("The selected user does not exist.", "projectpress"),
            );
        }
        $role = sanitize_key($role);
        if (!in_array($role, ["manager", "member", "viewer"], true)) {
            return new WP_Error(
                "projectpress_team_role_invalid",
                __("The team member role is invalid.", "projectpress"),
            );
        }
        if (!$this->repository->add_member($team_id, $user_id, $role)) {
            return new WP_Error(
                "projectpress_team_member_failed",
                __("The team member could not be saved.", "projectpress"),
            );
        }
        do_action("projectpress_team_member_saved", $team_id, $user_id, $role);
        return true;
    }

    /**
     * Remove a member from a team.
     *
     * @param int $team_id Team ID.
     * @param int $user_id User ID.
     * @return true|WP_Error True on success or WP_Error on failure.
     * @since 1.0.0
     */
    public function remove_member(int $team_id, int $user_id) {
        if (!$this->can_manage()) {
            return new WP_Error(
                "projectpress_team_forbidden",
                __("You do not have permission to manage teams.", "projectpress"),
            );
        }
        if (!$this->repository->remove_member($team_id, $user_id)) {
            return new WP_Error(
                "projectpress_team_member_remove_failed",
                __("The team member could not be removed.", "projectpress"),
            );
        }
        do_action("projectpress_team_member_removed", $team_id, $user_id);
        return true;
    }

    /**
     * Assign a project to a team.
     *
     * @param int $team_id Team ID.
     * @param int $project_id Project ID.
     * @param string $role Project role.
     * @return true|WP_Error True on success or WP_Error on failure.
     * @since 1.0.0
     */
    public function assign_project(int $team_id, int $project_id, string $role = "contributor") {
        if (!$this->can_manage()) {
            return new WP_Error(
                "projectpress_team_forbidden",
                __("You do not have permission to manage teams.", "projectpress"),
            );
        }
        if (!$this->repository->find($team_id)) {
            return new WP_Error("projectpress_team_not_found", __("Team not found.", "projectpress"));
        }
        $project = $this->projects->find($project_id);
        if (!$project) {
            return new WP_Error("projectpress_project_not_found", __("Project not found.", "projectpress"));
        }
        if (!$this->project_permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_project_assignment_forbidden",
                __("You do not have permission to assign this project.", "projectpress"),
            );
        }
        $role = sanitize_key($role);
        if (!in_array($role, ["owner", "manager", "contributor", "viewer"], true)) {
            return new WP_Error(
                "projectpress_team_project_role_invalid",
                __("The project team role is invalid.", "projectpress"),
            );
        }
        if (!$this->repository->assign_project($team_id, $project_id, $role)) {
            return new WP_Error(
                "projectpress_team_project_failed",
                __("The team could not be assigned to the project.", "projectpress"),
            );
        }
        do_action("projectpress_team_project_assigned", $team_id, $project_id, $role);
        return true;
    }

    /**
     * Remove a project from a team.
     *
     * @param int $team_id Team ID.
     * @param int $project_id Project ID.
     * @return true|WP_Error True on success or WP_Error on failure.
     * @since 1.0.0
     */
    public function remove_project(int $team_id, int $project_id) {
        if (!$this->can_manage()) {
            return new WP_Error(
                "projectpress_team_forbidden",
                __("You do not have permission to manage teams.", "projectpress"),
            );
        }
        $project = $this->projects->find($project_id);
        if (!$project || !$this->project_permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_project_assignment_forbidden",
                __("You do not have permission to change this project assignment.", "projectpress"),
            );
        }
        if (!$this->repository->remove_project($team_id, $project_id)) {
            return new WP_Error(
                "projectpress_team_project_remove_failed",
                __("The team project assignment could not be removed.", "projectpress"),
            );
        }
        do_action("projectpress_team_project_removed", $team_id, $project_id);
        return true;
    }

    /**
     * Check if the current user can manage teams.
     *
     * @return bool True if the user can manage teams, false otherwise.
     * @since 1.0.0
     */
    private function can_manage(): bool {
        return PermissionHelper::can_any(["projectpress_teams_manage", "manage_options"]);
    }
}
