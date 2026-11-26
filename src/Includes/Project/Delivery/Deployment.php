<?php

/**
 * Represents a ProjectPress deployment record.
 *
 * @package ProjectPress\Includes\Project\Delivery
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Delivery;

use ProjectPress\Includes\Project\Core\ProjectRecord;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Deployment domain component.
 *
 * @since 1.0.0
 */
final class Deployment extends ProjectRecord {
    /**
     * Get environment id.
     *
     * @return int
     * @since 1.0.0
     */
    public function get_environment_id(): int {
        return absint($this->get("environment_id", 0));
    }
    /**
     * Get status.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_status(): string {
        return (string) $this->get("status", "pending");
    }
}
