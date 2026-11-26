<?php

/**
 * Persistence operations for ProjectPress milestone records.
 *
 * @package ProjectPress\Includes\Project\Work
 * @since 1.0.0
 */

/**
 * Milestone persistence repository.
 *
 * @package ProjectPress
 */

namespace ProjectPress\Includes\Project\Work;

use ProjectPress\Includes\Functions\Helpers\DBHelper;

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Milestone Repository domain component.
 *
 * @since 1.0.0
 */
final class MilestoneRepository {
    /**
     * For project.
     *
     * @param int $project_id Project id.
     * @return array
     * @since 1.0.0
     */
    public function for_project(int $project_id): array {
        return array_map(
            static fn($row) => new Milestone($row),
            DBHelper::get_all(
                "milestones",
                ["project_id" => absint($project_id)],
                "*",
                "sort_order ASC, created_at ASC",
            ),
        );
    }

    /**
     * Find.
     *
     * @param int $id Id.
     * @return ?Milestone
     * @since 1.0.0
     */
    public function find(int $id): ?Milestone {
        $row = DBHelper::get_by_id("milestones", absint($id));
        return $row ? new Milestone($row) : null;
    }

    /**
     * Create.
     *
     * @param array $data Data.
     * @return mixed
     * @since 1.0.0
     */
    public function create(array $data) {
        return DBHelper::insert("milestones", $data);
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
        return DBHelper::update("milestones", $data, ["id" => absint($id)]);
    }

    /**
     * Delete.
     *
     * @param int $id Id.
     * @return bool
     * @since 1.0.0
     */
    public function delete(int $id): bool {
        return DBHelper::delete("milestones", ["id" => absint($id)]);
    }
}
