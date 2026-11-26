<?php

/**
 * Persistence operations for ProjectPress user story records.
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
 * User Story Repository domain component.
 *
 * @since 1.0.0
 */
final class UserStoryRepository {
    /**
     * Find.
     *
     * @param int $id Id.
     * @return ?UserStory
     * @since 1.0.0
     */
    public function find(int $id): ?UserStory {
        $row = DBHelper::get_by_id("user_stories", absint($id));
        return is_array($row) ? new UserStory($row) : null;
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
            static fn(array $row): UserStory => new UserStory($row),
            DBHelper::get_all(
                "user_stories",
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
        $id = DBHelper::insert(
            "user_stories",
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
     * @param UserStory $story Story.
     * @param array $data Data.
     * @return bool
     * @since 1.0.0
     */
    public function update(UserStory $story, array $data): bool {
        return DBHelper::update(
            "user_stories",
            array_merge($data, ["updated_by" => get_current_user_id(), "updated_at" => current_time("mysql", true)]),
            ["id" => $story->get_id(), "project_id" => $story->get_project_id()],
        );
    }
    /**
     * Delete.
     *
     * @param UserStory $story Story.
     * @return bool
     * @since 1.0.0
     */
    public function delete(UserStory $story): bool {
        return DBHelper::delete("user_stories", ["id" => $story->get_id(), "project_id" => $story->get_project_id()]);
    }
}
