<?php
/**
 * SBOM for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Composition;

use ProjectPress\Includes\Project\Core\ProjectRecord;
if (!defined("ABSPATH")) {
    exit();
}
final class SBOM extends ProjectRecord {
    /**
     * get_format.
     *
     * @return string get_format result.
     *
     * @since 1.0.0
     */
    public function get_format(): string {
        return (string) $this->get("format", "");
    }
    /**
     * get_components.
     *
     * @return array get_components result.
     *
     * @since 1.0.0
     */
    public function get_components(): array {
        return (array) $this->get("components", []);
    }
}
