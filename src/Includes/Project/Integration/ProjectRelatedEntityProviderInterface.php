<?php
/**
 * ProjectRelatedEntityProviderInterface for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Integration;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * ProjectRelatedEntityProviderInterface implementation.
 *
 * @since 1.0.0
 */
interface ProjectRelatedEntityProviderInterface {
    /** @return array<string,array<string,mixed>> */
    /**
     * get_project_related_entities.
     *
     * @return array get_project_related_entities result.
     *
     * @since 1.0.0
     */
    public function get_project_related_entities(): array;
}
