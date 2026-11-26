<?php
/**
 * Provenance for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Composition;

use ProjectPress\Includes\Project\Core\ProjectRecord;
if (!defined("ABSPATH")) {
    exit();
}
final class Provenance extends ProjectRecord {
    /**
     * get_source.
     *
     * @return string get_source result.
     *
     * @since 1.0.0
     */
    public function get_source(): string {
        return (string) $this->get("source", "");
    }
    /**
     * get_reference.
     *
     * @return string get_reference result.
     *
     * @since 1.0.0
     */
    public function get_reference(): string {
        return (string) $this->get("reference", "");
    }
}
