<?php

/**
 * Represents a ProjectPress milestone record.
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
 * Milestone domain component.
 *
 * @since 1.0.0
 */
final class Milestone extends ProjectRecord {
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
        return (string) $this->get("status", "planned");
    }
    /**
     * Get target date.
     *
     * @return ?string
     * @since 1.0.0
     */
    public function get_target_date(): ?string {
        $v = $this->get("target_date");
        return null === $v || "" === $v ? null : (string) $v;
    }
}
