<?php
/**
 * Document for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Collaboration;

use ProjectPress\Includes\Project\Core\ProjectRecord;
if (!defined("ABSPATH")) {
    exit();
}
final class Document extends ProjectRecord {
    /**
     * get_title.
     *
     * @return string get_title result.
     *
     * @since 1.0.0
     */
    public function get_title(): string {
        return (string) $this->get("title", "");
    }
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
}
