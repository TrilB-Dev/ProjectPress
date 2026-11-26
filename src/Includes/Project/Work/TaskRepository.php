<?php

/**
 * Persistence operations for ProjectPress task records.
 *
 * @package ProjectPress\Includes\Project\Work
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Work;

use ProjectPress\Includes\Functions\Helpers\DBHelper;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Task Repository domain component.
 *
 * @since 1.0.0
 */
final class TaskRepository {
    /**
     * For project.
     *
     * @param int $project_id Project id.
     * @return array
     * @since 1.0.0
     */
    public function for_project(int $project_id): array {
        return array_map(
            static fn($row) => new Task($row),
            DBHelper::get_all("tasks", ["project_id" => absint($project_id)], "*", "sort_order ASC, created_at ASC"),
        );
    }
    /**
     * Find.
     *
     * @param int $id Id.
     * @return ?Task
     * @since 1.0.0
     */
    public function find(int $id): ?Task {
        $row = DBHelper::get_by_id("tasks", absint($id));
        return $row ? new Task($row) : null;
    }
    /**
     * Create.
     *
     * @param array $data Data.
     * @return mixed
     * @since 1.0.0
     */
    public function create(array $data) {
        return DBHelper::insert("tasks", $data);
    }
    /**
     * Update.
     *
     * @param int $id Id.
     * @param array $data Data.
     * @return bool
     * @since 1.0.0
     */
    public function update(int $id, array $data): bool {
        return DBHelper::update("tasks", $data, ["id" => absint($id)]);
    }
    /**
     * Delete.
     *
     * @param int $id Id.
     * @return bool
     * @since 1.0.0
     */
    public function delete(int $id): bool {
        return DBHelper::delete("tasks", ["id" => absint($id)]);
    }
}
