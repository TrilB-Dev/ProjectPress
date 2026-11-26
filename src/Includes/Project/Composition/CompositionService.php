<?php
/**
 * CompositionService for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Composition;

use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\ProjectRepository;
use WP_Error;
if (!defined("ABSPATH")) {
    exit();
}
/**
 * CompositionService implementation.
 *
 * @since 1.0.0
 */
final class CompositionService {
    /**
     * type property.
     *
     * @var string
     * @since 1.0.0
     */
    private string $type;
    /**
     * records property.
     *
     * @var CompositionRepository
     * @since 1.0.0
     */
    private CompositionRepository $records;
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
     * @param string $type type parameter.
     * @param CompositionRepository $records records parameter.
     * @param ProjectRepository $projects projects parameter.
     * @param ProjectPermissions $permissions permissions parameter.
     *
     * @since 1.0.0
     */
    public function __construct(
        string $type,
        CompositionRepository $records,
        ProjectRepository $projects,
        ProjectPermissions $permissions,
    ) {
        $this->type = $type;
        $this->records = $records;
        $this->projects = $projects;
        $this->permissions = $permissions;
    }
    /**
     * create.
     *
     * @param int $id id parameter.
     * @param array $input input parameter.
     *
     * @return mixed create result.
     *
     * @since 1.0.0
     */
    public function create(int $id, array $input) {
        $project = $this->projects->find($id);
        if (!$project) {
            return new WP_Error("projectpress_project_not_found", __("Project not found.", "projectpress"));
        }
        if (!$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_composition_forbidden",
                __("You cannot change composition records for this project.", "projectpress"),
            );
        }
        $data = $this->normalise($input);
        return is_wp_error($data) ? $data : $this->records->create($id, $data);
    }
    /**
     * update.
     *
     * @param mixed $record record parameter.
     * @param array $input input parameter.
     *
     * @return mixed update result.
     *
     * @since 1.0.0
     */
    public function update($record, array $input) {
        $project = $this->projects->find($record->get_project_id());
        if (!$project || !$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_composition_forbidden",
                __("You cannot change composition records for this project.", "projectpress"),
            );
        }
        $data = $this->normalise($input);
        if (is_wp_error($data)) {
            return $data;
        }
        return $this->records->update($record, $data) ? $this->records->find($record->get_id()) : false;
    }
    /**
     * delete.
     *
     * @param mixed $record record parameter.
     *
     * @return mixed delete result.
     *
     * @since 1.0.0
     */
    public function delete($record) {
        $project = $this->projects->find($record->get_project_id());
        if (!$project || !$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_composition_forbidden",
                __("You cannot change composition records for this project.", "projectpress"),
            );
        }
        return $this->records->delete($record);
    }
    /**
     * normalise.
     *
     * @param array $input input parameter.
     *
     * @return mixed normalise result.
     *
     * @since 1.0.0
     */
    private function normalise(array $input) {
        $name = sanitize_text_field((string) ($input["name"] ?? ""));
        if ("relationship" !== $this->type && "sbom" !== $this->type && "" === $name) {
            return new WP_Error("projectpress_composition_name_required", __("A name is required.", "projectpress"));
        }
        return [
            "name" => $name,
            "version" => sanitize_text_field((string) ($input["version"] ?? "")),
            "description" => sanitize_textarea_field((string) ($input["description"] ?? "")),
            "identifier" => sanitize_text_field((string) ($input["identifier"] ?? "")),
            "uri" => esc_url_raw((string) ($input["uri"] ?? "")),
            "licence" => sanitize_text_field((string) ($input["licence"] ?? "")),
            "source_type" => sanitize_key((string) ($input["source_type"] ?? "")),
            "source_id" => absint($input["source_id"] ?? 0),
            "target_type" => sanitize_key((string) ($input["target_type"] ?? "")),
            "target_id" => absint($input["target_id"] ?? 0),
            "relationship_type" => sanitize_key((string) ($input["relationship_type"] ?? "")),
            "format" => sanitize_key((string) ($input["format"] ?? "generic")),
            "payload" => wp_json_encode($input["payload"] ?? []),
        ];
    }
}
