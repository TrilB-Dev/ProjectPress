<?php

/**
 * Represents a ProjectPress notification record.
 *
 * @package ProjectPress\Includes\Project\Communication
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Communication;

use ProjectPress\Includes\Project\Core\ProjectRecord;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Notification domain component.
 *
 * @since 1.0.0
 */
final class Notification extends ProjectRecord {
    /**
     * Get recipient id.
     *
     * @return int
     * @since 1.0.0
     */
    public function get_recipient_id(): int {
        return absint($this->get("recipient_id", 0));
    }
    /**
     * Get message.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_message(): string {
        return (string) $this->get("message", "");
    }
    /**
     * Is read.
     *
     * @return bool
     * @since 1.0.0
     */
    public function is_read(): bool {
        return (bool) $this->get("is_read", false);
    }
}
