<?php

/**
 * Persistence operations for ProjectPress notification records.
 *
 * @package ProjectPress\Includes\Project\Communication
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Communication;

use ProjectPress\Includes\Functions\Helpers\DBHelper;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Notification Repository domain component.
 *
 * @since 1.0.0
 */
final class NotificationRepository {
    /**
     * Find.
     *
     * @param int $id Id.
     * @return ?Notification
     * @since 1.0.0
     */
    public function find(int $id): ?Notification {
        $row = DBHelper::get_by_id("project_notifications", absint($id));
        return is_array($row) ? new Notification($row) : null;
    }
    /**
     * For project.
     *
     * @param int $id Id.
     * @return array
     * @since 1.0.0
     */
    public function for_project(int $id): array {
        return array_map(
            static fn(array $row): Notification => new Notification($row),
            DBHelper::get_all("project_notifications", ["project_id" => absint($id)], "*", "created_at DESC"),
        );
    }
    /**
     * Create.
     *
     * @param int $id Id.
     * @param array $data Data.
     * @return mixed
     * @since 1.0.0
     */
    public function create(int $id, array $data) {
        $record = DBHelper::insert(
            "project_notifications",
            array_merge($data, [
                "project_id" => absint($id),
                "created_by" => get_current_user_id(),
                "created_at" => current_time("mysql", true),
            ]),
        );
        return false === $record ? false : $this->find((int) $record);
    }
    /**
     * Delete.
     *
     * @param Notification $record Record.
     * @return bool
     * @since 1.0.0
     */
    public function delete(Notification $record): bool {
        return DBHelper::delete("project_notifications", [
            "id" => $record->get_id(),
            "project_id" => $record->get_project_id(),
        ]);
    }
}
