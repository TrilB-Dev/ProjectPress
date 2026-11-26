<?php

/**
 * Represents a ProjectPress activity log record.
 *
 * @package ProjectPress\Includes\Project\Tracking
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Tracking;

use ProjectPress\Includes\Functions\Helpers\DBHelper;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Activity Log domain component.
 *
 * @since 1.0.0
 */
final class ActivityLog {
    /**
     * For project.
     *
     * @param int $project_id Project id.
     * @param int $limit Limit.
     * @return array
     * @since 1.0.0
     */
    public static function for_project(int $project_id, int $limit = 0): array {
        $rows = DBHelper::get_all("activity", ["project_id" => absint($project_id)], "*", "created_at DESC");
        return $limit > 0 ? array_slice($rows, 0, $limit) : $rows;
    }
}
