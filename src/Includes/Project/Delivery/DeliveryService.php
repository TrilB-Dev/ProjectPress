<?php

/**
 * Business services for ProjectPress delivery records.
 *
 * @package ProjectPress\Includes\Project\Delivery
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Delivery;

use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\ProjectRepository;
use WP_Error;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Delivery Service domain component.
 *
 * @since 1.0.0
 */
final class DeliveryService {
    /**
     * Type.
     *
     * @var string
     * @since 1.0.0
     */
    private string $type;
    /**
     * Records.
     *
     * @var DeliveryRepository
     * @since 1.0.0
     */
    private DeliveryRepository $records;
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
     * @param string $type Type.
     * @param DeliveryRepository $records Records.
     * @param ProjectRepository $projects Projects.
     * @param ProjectPermissions $permissions Permissions.
     * @return void
     * @since 1.0.0
     */
    public function __construct(
        string $type,
        DeliveryRepository $records,
        ProjectRepository $projects,
        ProjectPermissions $permissions,
    ) {
        $this->type = $type;
        $this->records = $records;
        $this->projects = $projects;
        $this->permissions = $permissions;
    }
    /**
     * Create.
     *
     * @param int $id Id.
     * @param array $input Input.
     * @return mixed
     * @since 1.0.0
     */
    public function create(int $id, array $input) {
        $project = $this->projects->find($id);
        if (!$project) {
            return new WP_Error("projectpress_project_not_found", __("Project not found.", "projectpress"));
        }
        if (!$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_delivery_forbidden",
                __("You cannot change delivery records for this project.", "projectpress"),
            );
        }
        $data = $this->validate($input);
        return is_wp_error($data) ? $data : $this->records->create($id, $data);
    }
    /**
     * Update.
     *
     * @param mixed $record Record.
     * @param array $input Input.
     * @return mixed
     * @since 1.0.0
     */
    public function update($record, array $input) {
        $project = $this->projects->find($record->get_project_id());
        if (!$project || !$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_delivery_forbidden",
                __("You cannot change delivery records for this project.", "projectpress"),
            );
        }
        $data = $this->validate($input);
        if (is_wp_error($data)) {
            return $data;
        }
        return $this->records->update($record, $data) ? $this->records->find($record->get_id()) : false;
    }
    /**
     * Delete.
     *
     * @param mixed $record Record.
     * @return mixed
     * @since 1.0.0
     */
    public function delete($record) {
        $project = $this->projects->find($record->get_project_id());
        if (!$project || !$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_delivery_forbidden",
                __("You cannot change delivery records for this project.", "projectpress"),
            );
        }
        return $this->records->delete($record);
    }
    /**
     * Validate.
     *
     * @param array $input Input.
     * @return mixed
     * @since 1.0.0
     */
    private function validate(array $input) {
        $name = sanitize_text_field((string) ($input["name"] ?? ""));
        if ("" === $name) {
            return new WP_Error("projectpress_delivery_name_required", __("A name is required.", "projectpress"));
        }
        return [
            "name" => $name,
            "description" => sanitize_textarea_field((string) ($input["description"] ?? "")),
            "status" => sanitize_key((string) ($input["status"] ?? "planned")),
            "version" => sanitize_text_field((string) ($input["version"] ?? "")),
            "start_date" => sanitize_text_field((string) ($input["start_date"] ?? "")),
            "end_date" => sanitize_text_field((string) ($input["end_date"] ?? "")),
            "environment_id" => absint($input["environment_id"] ?? 0),
            "target_type" => sanitize_key((string) ($input["target_type"] ?? "")),
            "target_id" => absint($input["target_id"] ?? 0),
        ];
    }
}
