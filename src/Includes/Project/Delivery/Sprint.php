<?php

/**
 * Represents a ProjectPress sprint record.
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
 * Sprint domain component.
 *
 * @since 1.0.0
 */
final class Sprint extends ProjectRecord {
    /**
     * Get name.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_name(): string {
        return (string) $this->get("name", "");
    }
    /**
     * Get status.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_status(): string {
        return (string) $this->get("status", "planned");
    }
}
