<?php

/**
 * Represents a ProjectPress environment record.
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
 * Environment domain component.
 *
 * @since 1.0.0
 */
final class Environment extends ProjectRecord {
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
     * Get type.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_type(): string {
        return (string) $this->get("type", "");
    }
}
