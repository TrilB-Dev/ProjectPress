<?php

/**
 * Represents a ProjectPress test case record.
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
 * Test Case domain component.
 *
 * @since 1.0.0
 */
final class TestCase extends ProjectRecord {
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
        return (string) $this->get("status", "pending");
    }
}
