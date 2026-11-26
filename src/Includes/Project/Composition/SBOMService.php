<?php
/**
 * SBOMService for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Composition;

use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\ProjectRepository;
if (!defined("ABSPATH")) {
    exit();
}
/**
 * SBOMService implementation.
 *
 * @since 1.0.0
 */
final class SBOMService {
    /**
     * projects property.
     *
     * @var ProjectRepository
     * @since 1.0.0
     */
    private ProjectRepository $projects;
    /**
     * permissions property.
     *
     * @var ProjectPermissions
     * @since 1.0.0
     */
    private ProjectPermissions $permissions;
    /**
     * __construct.
     *
     * @param ProjectRepository $projects projects parameter.
     * @param ProjectPermissions $permissions permissions parameter.
     *
     * @since 1.0.0
     */
    public function __construct(ProjectRepository $projects, ProjectPermissions $permissions) {
        $this->projects = $projects;
        $this->permissions = $permissions;
    }
    /**
     * snapshot.
     *
     * @param int $id id parameter.
     *
     * @return ?array snapshot result.
     *
     * @since 1.0.0
     */
    public function snapshot(int $id): ?array {
        $project = $this->projects->find(absint($id));
        if (!$project || !$this->permissions->can_view($project)) {
            return null;
        }
        $data = [
            "project" => $project->to_array(),
            "components" => $this->rows("component", $id),
            "packages" => $this->rows("package", $id),
            "assets" => $this->rows("asset", $id),
            "licences" => $this->rows("licence", $id),
            "attributions" => $this->rows("attribution", $id),
            "provenance" => $this->rows("provenance", $id),
            "relationships" => $this->rows("relationship", $id),
        ];
        return apply_filters("projectpress_composition_snapshot", $data, $project);
    }
    /**
     * rows.
     *
     * @param string $type type parameter.
     * @param int $id id parameter.
     *
     * @return array rows result.
     *
     * @since 1.0.0
     */
    private function rows(string $type, int $id): array {
        return array_map(static fn($row) => $row->to_array(), new CompositionRepository($type)->for_project($id));
    }
}
