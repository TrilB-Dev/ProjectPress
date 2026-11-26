<?php

/**
 * Persistence operations for ProjectPress time sheet records.
 *
 * @package ProjectPress\Includes\Project\People
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\People;

use ProjectPress\Includes\Functions\Helpers\DBHelper;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Time Sheet Repository domain component.
 *
 * @since 1.0.0
 */
final class TimeSheetRepository {
    /**
     * Find.
     *
     * @param int $id Id.
     * @return ?TimeSheet
     * @since 1.0.0
     */
    public function find(int $id): ?TimeSheet {
        $row = DBHelper::get_by_id("timesheets", absint($id));
        return is_array($row) ? new TimeSheet($row) : null;
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
            static fn(array $row): TimeSheet => new TimeSheet($row),
            DBHelper::get_all("timesheets", ["project_id" => absint($id)], "*", "work_date DESC,created_at DESC"),
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
        $now = current_time("mysql", true);
        $record = DBHelper::insert(
            "timesheets",
            array_merge($data, [
                "project_id" => absint($id),
                "created_by" => get_current_user_id(),
                "created_at" => $now,
                "updated_at" => $now,
            ]),
        );
        return false === $record ? false : $this->find((int) $record);
    }
    /**
     * Delete.
     *
     * @param TimeSheet $record Record.
     * @return bool
     * @since 1.0.0
     */
    public function delete(TimeSheet $record): bool {
        return DBHelper::delete("timesheets", ["id" => $record->get_id(), "project_id" => $record->get_project_id()]);
    }
}
