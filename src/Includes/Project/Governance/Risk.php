<?php

/**
 * Represents a ProjectPress risk record.
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
 * Risk domain component.
 *
 * @since 1.0.0
 */
final class Risk extends ProjectRecord {
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
     * Get probability.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_probability(): string {
        return (string) $this->get("probability", "");
    }
    /**
     * Get impact.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_impact(): string {
        return (string) $this->get("impact", "");
    }
}
