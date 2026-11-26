<?php

/**
 * Persistence operations for ProjectPress requirement records.
 *
 * @package ProjectPress\Includes\Project\Requirements
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Requirements;

use ProjectPress\Includes\Functions\Helpers\DBHelper;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Requirement Repository domain component.
 *
 * @since 1.0.0
 */
final class RequirementRepository {
    /**
     * Find.
     *
     * @param int $id Id.
     * @return ?Requirement
     * @since 1.0.0
     */
    public function find(int $id): ?Requirement {
        $row = DBHelper::get_by_id("requirements", absint($id));
        return is_array($row) ? new Requirement($row) : null;
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
            static fn(array $row): Requirement => new Requirement($row),
            DBHelper::get_all(
                "requirements",
                ["project_id" => absint($project_id)],
                "*",
                "sort_order ASC,created_at ASC",
            ),
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
        $id = DBHelper::insert("requirements", [
            "project_id" => absint($project_id),
            "title" => sanitize_text_field((string) ($data["title"] ?? "")),
            "description" => sanitize_textarea_field((string) ($data["description"] ?? "")),
            "status" => sanitize_key((string) ($data["status"] ?? "draft")),
            "priority" => sanitize_key((string) ($data["priority"] ?? "normal")),
            "sort_order" => absint($data["sort_order"] ?? 0),
            "created_by" => get_current_user_id(),
            "created_at" => $now,
            "updated_by" => get_current_user_id(),
            "updated_at" => $now,
        ]);
        return false === $id ? false : $this->find((int) $id);
    }
    /**
     * Update.
     *
     * @param Requirement $requirement Requirement.
     * @param array $data Data.
     * @return bool
     * @since 1.0.0
     */
    public function update(Requirement $requirement, array $data): bool {
        return DBHelper::update(
            "requirements",
            array_merge($data, ["updated_by" => get_current_user_id(), "updated_at" => current_time("mysql", true)]),
            ["id" => $requirement->get_id()],
        );
    }
    /**
     * Delete.
     *
     * @param Requirement $requirement Requirement.
     * @return bool
     * @since 1.0.0
     */
    public function delete(Requirement $requirement): bool {
        return DBHelper::delete("requirements", [
            "id" => $requirement->get_id(),
            "project_id" => $requirement->get_project_id(),
        ]);
    }
}
