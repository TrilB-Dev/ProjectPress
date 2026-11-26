<?php

/**
 * Business services for ProjectPress feature records.
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
 * Feature Service domain component.
 *
 * @since 1.0.0
 */
final class FeatureService {
    /**
     * Features.
     *
     * @var FeatureRepository
     * @since 1.0.0
     */
    private FeatureRepository $features;
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
     * @param FeatureRepository $features Features.
     * @param ProjectRepository $projects Projects.
     * @param ProjectPermissions $permissions Permissions.
     * @return void
     * @since 1.0.0
     */
    public function __construct(
        FeatureRepository $features,
        ProjectRepository $projects,
        ProjectPermissions $permissions,
    ) {
        $this->features = $features;
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
        return ["planned", "in_progress", "completed", "cancelled"];
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
                "projectpress_feature_forbidden",
                __("You cannot change features for this project.", "projectpress"),
            );
        }
        $data = $this->validate($input);
        return is_wp_error($data) ? $data : $this->features->create($project_id, $data);
    }
    /**
     * Update.
     *
     * @param Feature $feature Feature.
     * @param array $input Input.
     * @return mixed
     * @since 1.0.0
     */
    public function update(Feature $feature, array $input) {
        $project = $this->projects->find($feature->get_project_id());
        if (!$project || !$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_feature_forbidden",
                __("You cannot change features for this project.", "projectpress"),
            );
        }
        $data = $this->validate($input);
        if (is_wp_error($data)) {
            return $data;
        }
        return $this->features->update($feature, $data) ? $this->features->find($feature->get_id()) : false;
    }
    /**
     * Delete.
     *
     * @param Feature $feature Feature.
     * @return mixed
     * @since 1.0.0
     */
    public function delete(Feature $feature) {
        $project = $this->projects->find($feature->get_project_id());
        if (!$project || !$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_feature_forbidden",
                __("You cannot change features for this project.", "projectpress"),
            );
        }
        return $this->features->delete($feature);
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
                "projectpress_feature_title_required",
                __("A feature title is required.", "projectpress"),
            );
        }
        $status = sanitize_key((string) ($input["status"] ?? "planned"));
        if (!in_array($status, self::statuses(), true)) {
            return new WP_Error(
                "projectpress_feature_status_invalid",
                __("The feature status is invalid.", "projectpress"),
            );
        }
        return [
            "title" => $title,
            "description" => sanitize_textarea_field((string) ($input["description"] ?? "")),
            "status" => $status,
            "sort_order" => absint($input["sort_order"] ?? 0),
        ];
    }
}
