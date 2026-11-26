<?php
/**
 * ProjectExtensionProviderInterface for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Integration;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * ProjectExtensionProviderInterface implementation.
 *
 * @since 1.0.0
 */
interface ProjectExtensionProviderInterface {
    /**
     * register_project_extensions.
     *
     * @param ProjectExtensionRegistry $registry registry parameter.
     *
     * @return void register_project_extensions result.
     *
     * @since 1.0.0
     */
    public function register_project_extensions(ProjectExtensionRegistry $registry): void;
}
