<?php

/**
 * Business services for ProjectPress notification records.
 *
 * @package ProjectPress\Includes\Project\Communication
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Communication;

use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\ProjectRepository;
use WP_Error;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Notification Service domain component.
 *
 * @since 1.0.0
 */
final class NotificationService {
    /**
     * Records.
     *
     * @var NotificationRepository
     * @since 1.0.0
     */
    private NotificationRepository $records;
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
     * @param NotificationRepository $records Records.
     * @param ProjectRepository $projects Projects.
     * @param ProjectPermissions $permissions Permissions.
     * @return void
     * @since 1.0.0
     */
    public function __construct(
        NotificationRepository $records,
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
                "projectpress_notification_forbidden",
                __("You cannot change notifications for this project.", "projectpress"),
            );
        }
        $recipient = absint($input["recipient_id"] ?? 0);
        $message = sanitize_textarea_field((string) ($input["message"] ?? ""));
        if ($recipient < 1 || "" === $message) {
            return new WP_Error(
                "projectpress_notification_invalid",
                __("A recipient and message are required.", "projectpress"),
            );
        }
        return $this->records->create($id, [
            "recipient_id" => $recipient,
            "message" => $message,
            "is_read" => 0,
            "target_type" => sanitize_key((string) ($input["target_type"] ?? "")),
            "target_id" => absint($input["target_id"] ?? 0),
        ]);
    }
    /**
     * Delete.
     *
     * @param Notification $record Record.
     * @return mixed
     * @since 1.0.0
     */
    public function delete(Notification $record) {
        $project = $this->projects->find($record->get_project_id());
        if (!$project || !$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_notification_forbidden",
                __("You cannot change notifications for this project.", "projectpress"),
            );
        }
        return $this->records->delete($record);
    }
}
