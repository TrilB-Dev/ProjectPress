<?php
/**
 * Package for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Composition;

use ProjectPress\Includes\Project\Core\ProjectRecord;
if (!defined("ABSPATH")) {
    exit();
}
final class Package extends ProjectRecord {
    /**
     * get_name.
     *
     * @return string get_name result.
     *
     * @since 1.0.0
     */
    public function get_name(): string {
        return (string) $this->get("name", "");
    }
    /**
     * get_version.
     *
     * @return string get_version result.
     *
     * @since 1.0.0
     */
    public function get_version(): string {
        return (string) $this->get("version", "");
    }
}
