<?php
/**
 * Project type administration.
 *
 * @package ProjectPress
 * @subpackage Admin\Manager\ProjectManager
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\ProjectManager;

use ProjectPress\Includes\Pages\Taxonomies;

if (!defined("ABSPATH")) {
    exit();
}

final class Types extends Classifications {
    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        parent::__construct("pp_project_type", __("Project Types", "projectpress"));
    }
}
