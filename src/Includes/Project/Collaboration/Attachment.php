<?php
/**
 * Attachment for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Collaboration;

use ProjectPress\Includes\Project\Core\ProjectRecord;
if (!defined("ABSPATH")) {
    exit();
}
final class Attachment extends ProjectRecord {
    /**
     * get_attachment_id.
     *
     * @return int get_attachment_id result.
     *
     * @since 1.0.0
     */
    public function get_attachment_id(): int {
        return absint($this->get("attachment_id", 0));
    }
    /**
     * get_label.
     *
     * @return string get_label result.
     *
     * @since 1.0.0
     */
    public function get_label(): string {
        return (string) $this->get("file_label", "");
    }
}
