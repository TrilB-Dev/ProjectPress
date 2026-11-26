<?php

/**
 * Represents a ProjectPress time sheet record.
 *
 * @package ProjectPress\Includes\Project\People
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\People;

use ProjectPress\Includes\Project\Core\ProjectRecord;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Time Sheet domain component.
 *
 * @since 1.0.0
 */
final class TimeSheet extends ProjectRecord {
    /**
     * Get user id.
     *
     * @return int
     * @since 1.0.0
     */
    public function get_user_id(): int {
        return absint($this->get("user_id", 0));
    }
    /**
     * Get minutes.
     *
     * @return int
     * @since 1.0.0
     */
    public function get_minutes(): int {
        return max(0, (int) $this->get("minutes", 0));
    }
}
