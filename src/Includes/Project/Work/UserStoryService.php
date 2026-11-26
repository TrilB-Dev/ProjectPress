<?php

/**
 * Business services for ProjectPress user story records.
 *
 * @package ProjectPress\Includes\Project\Work
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Work;

use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\ProjectRepository;
use WP_Error;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * User Story Service domain component.
 *
 * @since 1.0.0
 */
final class UserStoryService {
    /**
     * Stories.
     *
     * @var UserStoryRepository
     * @since 1.0.0
     */
    private UserStoryRepository $stories;
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
     * @param UserStoryRepository $stories Stories.
     * @param ProjectRepository $projects Projects.
     * @param ProjectPermissions $permissions Permissions.
     * @return void
     * @since 1.0.0
     */
    public function __construct(
        UserStoryRepository $stories,
        ProjectRepository $projects,
        ProjectPermissions $permissions,
    ) {
        $this->stories = $stories;
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
        return ["backlog", "ready", "in_progress", "completed", "cancelled"];
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
                "projectpress_story_forbidden",
                __("You cannot change user stories for this project.", "projectpress"),
            );
        }
        $data = $this->validate($input);
        return is_wp_error($data) ? $data : $this->stories->create($project_id, $data);
    }
    /**
     * Update.
     *
     * @param UserStory $story Story.
     * @param array $input Input.
     * @return mixed
     * @since 1.0.0
     */
    public function update(UserStory $story, array $input) {
        $project = $this->projects->find($story->get_project_id());
        if (!$project || !$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_story_forbidden",
                __("You cannot change user stories for this project.", "projectpress"),
            );
        }
        $data = $this->validate($input);
        if (is_wp_error($data)) {
            return $data;
        }
        return $this->stories->update($story, $data) ? $this->stories->find($story->get_id()) : false;
    }
    /**
     * Delete.
     *
     * @param UserStory $story Story.
     * @return mixed
     * @since 1.0.0
     */
    public function delete(UserStory $story) {
        $project = $this->projects->find($story->get_project_id());
        if (!$project || !$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_story_forbidden",
                __("You cannot change user stories for this project.", "projectpress"),
            );
        }
        return $this->stories->delete($story);
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
                "projectpress_story_title_required",
                __("A user story title is required.", "projectpress"),
            );
        }
        $status = sanitize_key((string) ($input["status"] ?? "backlog"));
        if (!in_array($status, self::statuses(), true)) {
            return new WP_Error(
                "projectpress_story_status_invalid",
                __("The user story status is invalid.", "projectpress"),
            );
        }
        return [
            "title" => $title,
            "description" => sanitize_textarea_field((string) ($input["description"] ?? "")),
            "status" => $status,
            "points" => absint($input["points"] ?? 0),
            "sort_order" => absint($input["sort_order"] ?? 0),
        ];
    }
}
