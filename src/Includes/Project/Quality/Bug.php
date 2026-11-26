<?php

/**
 * Represents a ProjectPress bug record.
 *
 * @package ProjectPress\Includes\Project\Quality
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Quality;

use ProjectPress\Includes\Project\Core\ProjectRecord;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Bug domain component.
 *
 * @since 1.0.0
 */
final class Bug extends ProjectRecord {
    /**
     * Get title.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_title(): string {
        return (string) $this->get("title", "");
    }
    /**
     * Get severity.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_severity(): string {
        return (string) $this->get("severity", "normal");
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
