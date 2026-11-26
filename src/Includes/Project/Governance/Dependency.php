<?php

/**
 * Represents a ProjectPress dependency record.
 *
 * @package ProjectPress\Includes\Project\Governance
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Governance;

use ProjectPress\Includes\Project\Core\ProjectRecord;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Dependency domain component.
 *
 * @since 1.0.0
 */
final class Dependency extends ProjectRecord {
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
        return (string) $this->get("status", "open");
    }
}
