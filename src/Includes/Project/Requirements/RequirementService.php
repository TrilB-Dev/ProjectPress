<?php

/**
 * Business services for ProjectPress requirement records.
 *
 * @package ProjectPress\Includes\Project\Requirements
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Requirements;

use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\ProjectRepository;
use WP_Error;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Requirement Service domain component.
 *
 * @since 1.0.0
 */
final class RequirementService {
    /**
     * Requirements.
     *
     * @var RequirementRepository
     * @since 1.0.0
     */
    private RequirementRepository $requirements;
    /**
     * Projects.
     *
     * @var ProjectRepository
     * @since 1.0.0
     */
    private ProjectRepository $projects;
    /**
     * Permissions.
     *
     * @var ProjectPermissions
     * @since 1.0.0
     */
    private ProjectPermissions $permissions;
    /**
     * Construct.
     *
     * @param RequirementRepository $requirements Requirements.
     * @param ProjectRepository $projects Projects.
     * @param ProjectPermissions $permissions Permissions.
     * @return void
     * @since 1.0.0
     */
    public function __construct(
        RequirementRepository $requirements,
        ProjectRepository $projects,
        ProjectPermissions $permissions,
    ) {
        $this->requirements = $requirements;
        $this->projects = $projects;
        $this->permissions = $permissions;
    }
    /**
     * Statuses.
     *
     * @return array
     * @since 1.0.0
     */
    public static function statuses(): array {
        return ["draft", "approved", "in_progress", "completed", "rejected"];
    }
    /**
     * Priorities.
     *
     * @return array
     * @since 1.0.0
     */
    public static function priorities(): array {
        return ["low", "normal", "high", "urgent"];
    }
    /**
     * Create.
     *
     * @param int $project_id Project id.
     * @param array $input Input.
     * @return mixed
     * @since 1.0.0
     */
    public function create(int $project_id, array $input) {
        $project = $this->projects->find($project_id);
        if (!$project) {
            return new WP_Error("projectpress_project_not_found", __("Project not found.", "projectpress"));
        }
        if (!$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_requirement_forbidden",
                __("You cannot change requirements for this project.", "projectpress"),
            );
        }
        $data = $this->validate($input);
        if (is_wp_error($data)) {
            return $data;
        }
        return $this->requirements->create($project_id, $data);
    }
    /**
     * Delete.
     *
     * @param Requirement $requirement Requirement.
     * @return mixed
     * @since 1.0.0
     */
    public function delete(Requirement $requirement) {
        $project = $this->projects->find($requirement->get_project_id());
        if (!$project || !$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_requirement_forbidden",
                __("You cannot change requirements for this project.", "projectpress"),
            );
        }
        return $this->requirements->delete($requirement);
    }
    /**
     * Validate.
     *
     * @param array $input Input.
     * @return mixed
     * @since 1.0.0
     */
    private function validate(array $input) {
        $title = sanitize_text_field((string) ($input["title"] ?? ""));
        if ("" === $title) {
            return new WP_Error(
                "projectpress_requirement_title_required",
                __("A requirement title is required.", "projectpress"),
            );
        }
        $status = sanitize_key((string) ($input["status"] ?? "draft"));
        $priority = sanitize_key((string) ($input["priority"] ?? "normal"));
        if (!in_array($status, self::statuses(), true)) {
            return new WP_Error(
                "projectpress_requirement_status_invalid",
                __("The requirement status is invalid.", "projectpress"),
            );
        }
        if (!in_array($priority, self::priorities(), true)) {
            return new WP_Error(
                "projectpress_requirement_priority_invalid",
                __("The requirement priority is invalid.", "projectpress"),
            );
        }
        return [
            "title" => $title,
            "description" => sanitize_textarea_field((string) ($input["description"] ?? "")),
            "status" => $status,
            "priority" => $priority,
            "sort_order" => absint($input["sort_order"] ?? 0),
        ];
    }
}
