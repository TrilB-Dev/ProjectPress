<?php

/**
 * Represents a ProjectPress decision log record.
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
 * Decision Log domain component.
 *
 * @since 1.0.0
 */
final class DecisionLog extends ProjectRecord {
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
     * Get decision.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_decision(): string {
        return (string) $this->get("decision", "");
    }
}
