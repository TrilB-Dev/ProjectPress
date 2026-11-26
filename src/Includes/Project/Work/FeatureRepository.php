<?php

/**
 * Persistence operations for ProjectPress feature records.
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
 * Feature Repository domain component.
 *
 * @since 1.0.0
 */
final class FeatureRepository {
    /**
     * Find.
     *
     * @param int $id Id.
     * @return ?Feature
     * @since 1.0.0
     */
    public function find(int $id): ?Feature {
        $row = DBHelper::get_by_id("features", absint($id));
        return is_array($row) ? new Feature($row) : null;
    }
    /**
     * For project.
     *
     * @param int $project_id Project id.
     * @return array
     * @since 1.0.0
     */
    public function for_project(int $project_id): array {
        return array_map(
            static fn(array $row): Feature => new Feature($row),
            DBHelper::get_all("features", ["project_id" => absint($project_id)], "*", "sort_order ASC,created_at ASC"),
        );
    }
    /**
     * Create.
     *
     * @param int $project_id Project id.
     * @param array $data Data.
     * @return mixed
     * @since 1.0.0
     */
    public function create(int $project_id, array $data) {
        $now = current_time("mysql", true);
        $id = DBHelper::insert(
            "features",
            array_merge($data, [
                "project_id" => absint($project_id),
                "created_by" => get_current_user_id(),
                "created_at" => $now,
                "updated_by" => get_current_user_id(),
                "updated_at" => $now,
            ]),
        );
        return false === $id ? false : $this->find((int) $id);
    }
    /**
     * Update.
     *
     * @param Feature $feature Feature.
     * @param array $data Data.
     * @return bool
     * @since 1.0.0
     */
    public function update(Feature $feature, array $data): bool {
        return DBHelper::update(
            "features",
            array_merge($data, ["updated_by" => get_current_user_id(), "updated_at" => current_time("mysql", true)]),
            ["id" => $feature->get_id(), "project_id" => $feature->get_project_id()],
        );
    }
    /**
     * Delete.
     *
     * @param Feature $feature Feature.
     * @return bool
     * @since 1.0.0
     */
    public function delete(Feature $feature): bool {
        return DBHelper::delete("features", ["id" => $feature->get_id(), "project_id" => $feature->get_project_id()]);
    }
}
