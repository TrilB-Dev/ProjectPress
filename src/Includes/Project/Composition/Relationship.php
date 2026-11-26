<?php
/**
 * Relationship for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Composition;

use ProjectPress\Includes\Project\Core\ProjectRecord;
if (!defined("ABSPATH")) {
    exit();
}
final class Relationship extends ProjectRecord {
    /**
     * get_source_id.
     *
     * @return int get_source_id result.
     *
     * @since 1.0.0
     */
    public function get_source_id(): int {
        return absint($this->get("source_project_id", 0));
    }
    /**
     * get_target_id.
     *
     * @return int get_target_id result.
     *
     * @since 1.0.0
     */
    public function get_target_id(): int {
        return absint($this->get("target_project_id", 0));
    }
    /**
     * get_type.
     *
     * @return string get_type result.
     *
     * @since 1.0.0
     */
    public function get_type(): string {
        return (string) $this->get("relationship", "");
    }
}
