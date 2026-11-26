<?php

/**
 * Represents a ProjectPress work item record.
 *
 * @package ProjectPress\Includes\Project\Work
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Work;

use ProjectPress\Includes\Project\Core\ProjectRecord;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Work Item domain component.
 *
 * @since 1.0.0
 */
final class WorkItem extends ProjectRecord {
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
        return (string) $this->get("status", "open");
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
