<?php
/**
 * ProjectWorkspaceProviderInterface for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Integration;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * ProjectWorkspaceProviderInterface implementation.
 *
 * @since 1.0.0
 */
interface ProjectWorkspaceProviderInterface {
    /** @return array<string,array<string,mixed>> */
    /**
     * get_project_workspace_tabs.
     *
     * @return array get_project_workspace_tabs result.
     *
     * @since 1.0.0
     */
    public function get_project_workspace_tabs(): array;
}
