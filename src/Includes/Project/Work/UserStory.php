<?php

/**
 * Represents a ProjectPress user story record.
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
 * User Story domain component.
 *
 * @since 1.0.0
 */
final class UserStory extends ProjectRecord {
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
     * Get points.
     *
     * @return int
     * @since 1.0.0
     */
    public function get_points(): int {
        return max(0, (int) $this->get("points", 0));
    }
}
