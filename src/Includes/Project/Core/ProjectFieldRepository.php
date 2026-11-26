<?php
/**
 * Project field persistence repository.
 *
 * @package ProjectPress\Includes\Project\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Core;

use ProjectPress\Includes\Functions\Helpers\DBHelper;

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Reads and writes global project fields and field values.
 *
 * @since 1.0.0
 */
final class ProjectFieldRepository {
    /** @param bool $enabled_only @return array @since 1.0.0 */
    public function all(bool $enabled_only = false): array {
        return DBHelper::get_all(
            "project_fields",
            $enabled_only ? ["is_enabled" => 1] : [],
            "*",
            "sort_order ASC, field_name ASC",
        );
    }

    /**
     * Finds a project field by its ID.
     *
     * @param int $id
     * @return null|array
     * @since 1.0.0
     */
    public function find(int $id): ?array {
        return DBHelper::get_by_id("project_fields", absint($id));
    }

    /**
     * Creates a new project field.
     *
     * @param array $data
     * @return int|false
     * @since 1.0.0
     */
    public function create(array $data) {
        return DBHelper::insert("project_fields", $data);
    }

    /**
     * Updates an existing project field.
     *
     * @param int $id
     * @param array $data
     * @return bool
     * @since 1.0.0
     */
    public function update(int $id, array $data): bool {
        return DBHelper::update("project_fields", $data, ["id" => absint($id)]);
    }

    /**
     * Retrieves all field values for a given project.
     *
     * @param int $project_id
     * @return array
     * @since 1.0.0
     */
    public function values(int $project_id): array {
        $rows = DBHelper::get_all("project_field_values", ["project_id" => absint($project_id)]);
        $values = [];
        foreach ($rows as $row) {
            $values[(int) $row["field_id"]] = $row;
        }
        return $values;
    }

    /**
     * Retrieves the value of a specific field for a given project.
     *
     * @param int $project_id
     * @param int $field_id
     * @return array|null
     * @since 1.0.0
     */
    public function value(int $project_id, int $field_id): ?array {
        return DBHelper::get_row("project_field_values", [
            "project_id" => absint($project_id),
            "field_id" => absint($field_id),
        ]);
    }

    /**
     * Saves the value of a specific field for a given project.
     *
     * @param int $project_id
     * @param int $field_id
     * @param array $data
     * @return bool
     * @since 1.0.0
     */
    public function save_value(int $project_id, int $field_id, array $data): bool {
        $existing = $this->value($project_id, $field_id);
        $data = array_merge($data, [
            "updated_by" => get_current_user_id(),
            "updated_at" => current_time("mysql", true),
        ]);
        if ($existing) {
            return DBHelper::update("project_field_values", $data, ["id" => (int) $existing["id"]]);
        }
        $data = array_merge(
            ["project_id" => $project_id, "field_id" => $field_id, "created_at" => current_time("mysql", true)],
            $data,
        );
        return false !== DBHelper::insert("project_field_values", $data);
    }

    /**
     * Deletes the value of a specific field for a given project.
     *
     * @param int $project_id
     * @param int $field_id
     * @return bool
     * @since 1.0.0
     */
    public function delete_value(int $project_id, int $field_id): bool {
        return DBHelper::delete("project_field_values", ["project_id" => $project_id, "field_id" => $field_id]);
    }
}
