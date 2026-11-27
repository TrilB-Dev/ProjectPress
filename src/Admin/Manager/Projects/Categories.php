<?php
/**
 * Project category administration.
 *
 * @package ProjectPress
 * @subpackage Admin\Manager\Projects
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\Projects;

use ProjectPress\Includes\Pages\Taxonomies;

if (!defined("ABSPATH")) {
    exit();
}

final class Categories extends Classifications {
    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        parent::__construct("pp_project_category", __("Project Categories", "projectpress"));
    }
}
