<?php

/**
 * Represents a ProjectPress team record.
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
 * Team domain component.
 *
 * @since 1.0.0
 */
final class Team extends ProjectRecord {
    /**
     * Get name.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_name(): string {
        return (string) $this->get("team_name", $this->get("name", ""));
    }
    /**
     * Get owner id.
     *
     * @return int
     * @since 1.0.0
     */
    public function get_owner_id(): int {
        return absint($this->get("owner_id", 0));
    }
}
