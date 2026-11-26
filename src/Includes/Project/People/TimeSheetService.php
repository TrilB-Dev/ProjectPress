<?php

/**
 * Business services for ProjectPress time sheet records.
 *
 * @package ProjectPress\Includes\Project\People
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\People;

use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\ProjectRepository;
use WP_Error;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Time Sheet Service domain component.
 *
 * @since 1.0.0
 */
final class TimeSheetService {
    /**
     * Records.
     *
     * @var TimeSheetRepository
     * @since 1.0.0
     */
    private TimeSheetRepository $records;
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
     * @param TimeSheetRepository $records Records.
     * @param ProjectRepository $projects Projects.
     * @param ProjectPermissions $permissions Permissions.
     * @return void
     * @since 1.0.0
     */
    public function __construct(
        TimeSheetRepository $records,
        ProjectRepository $projects,
        ProjectPermissions $permissions,
    ) {
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
                "projectpress_timesheet_forbidden",
                __("You cannot change timesheets for this project.", "projectpress"),
            );
        }
        $user = absint($input["user_id"] ?? 0);
        $minutes = absint($input["minutes"] ?? 0);
        if ($user < 1 || $minutes < 1) {
            return new WP_Error(
                "projectpress_timesheet_invalid",
                __("A user and positive time value are required.", "projectpress"),
            );
        }
        return $this->records->create($id, [
            "user_id" => $user,
            "minutes" => $minutes,
            "work_date" => sanitize_text_field((string) ($input["work_date"] ?? "")),
            "description" => sanitize_textarea_field((string) ($input["description"] ?? "")),
            "target_type" => sanitize_key((string) ($input["target_type"] ?? "")),
            "target_id" => absint($input["target_id"] ?? 0),
        ]);
    }
    /**
     * Delete.
     *
     * @param TimeSheet $record Record.
     * @return mixed
     * @since 1.0.0
     */
    public function delete(TimeSheet $record) {
        $project = $this->projects->find($record->get_project_id());
        if (!$project || !$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_timesheet_forbidden",
                __("You cannot change timesheets for this project.", "projectpress"),
            );
        }
        return $this->records->delete($record);
    }
}
