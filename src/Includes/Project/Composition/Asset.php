<?php
/**
 * Asset for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Composition;

use ProjectPress\Includes\Project\Core\ProjectRecord;
if (!defined("ABSPATH")) {
    exit();
}
final class Asset extends ProjectRecord {
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
     * get_uri.
     *
     * @return string get_uri result.
     *
     * @since 1.0.0
     */
    public function get_uri(): string {
        return (string) $this->get("uri", "");
    }
}
