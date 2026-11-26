<?php
/**
 * Comment for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Collaboration;

use ProjectPress\Includes\Project\Core\ProjectRecord;
if (!defined("ABSPATH")) {
    exit();
}
final class Comment extends ProjectRecord {
    /**
     * get_content.
     *
     * @return string get_content result.
     *
     * @since 1.0.0
     */
    public function get_content(): string {
        return (string) $this->get("content", "");
    }
    /**
     * get_author_id.
     *
     * @return int get_author_id result.
     *
     * @since 1.0.0
     */
    public function get_author_id(): int {
        return absint($this->get("created_by", 0));
    }
}
