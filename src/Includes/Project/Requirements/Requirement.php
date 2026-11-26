<?php

/**
 * Represents a ProjectPress requirement record.
 *
 * @package ProjectPress\Includes\Project\Requirements
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Requirements;

use ProjectPress\Includes\Project\Core\ProjectRecord;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Requirement domain component.
 *
 * @since 1.0.0
 */
final class Requirement extends ProjectRecord {
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
     * Get status.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_status(): string {
        return (string) $this->get("status", "draft");
    }
    /**
     * Get priority.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_priority(): string {
        return (string) $this->get("priority", "normal");
    }
}
