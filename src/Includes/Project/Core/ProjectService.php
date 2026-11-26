<?php
/**
 * Project lifecycle service.
 *
 * @package ProjectPress\Includes\Project\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Core;

use ProjectPress\Includes\Core\Defaults;
use WP_Error;

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Applies project creation, update, archive, restore, and deletion rules.
 *
 * @since 1.0.0
 */
final class ProjectService {
    /**
     * Project repository.
     *
     * @var ProjectRepository
     * @since 1.0.0
     */
    private ProjectRepository $repository;
    /**
     * Project validator.
     *
     * @var ProjectValidator
     * @since 1.0.0
     */
    private ProjectValidator $validator;
    /**
     * Project permissions.
     *
     * @var ProjectPermissions
     * @since 1.0.0
     */
    private ProjectPermissions $permissions;

    /**
     * Initialise the project service.
     *
     * @param ProjectRepository  $repository Project repository.
     * @param ProjectValidator   $validator Project validator.
     * @param ProjectPermissions $permissions Project permissions.
     * @since 1.0.0
     */
    public function __construct(
        ProjectRepository $repository,
        ProjectValidator $validator,
        ProjectPermissions $permissions,
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->permissions = $permissions;
    }

    /**
     * Create a new project.
     *
     * @param array $input Project data.
     * @return Project|WP_Error
     * @since 1.0.0
     */
    public function create(array $input) {
        if (!$this->permissions->can_create()) {
            return new WP_Error(
                "projectpress_create_forbidden",
                __("You do not have permission to create projects.", "projectpress"),
            );
        }
        $data = $this->validator->validate($input);
        if (is_wp_error($data)) {
            return $data;
        }
        $data["slug"] = $data["slug"] ?? sanitize_title($data["name"]);
        $data["key"] = $data["key"] ?? strtoupper(substr(sanitize_key($data["name"]), 0, 24) . "-" . wp_unique_id());
        $data["owner_id"] = $data["owner_id"] ?? get_current_user_id();
        $data["created_by"] = get_current_user_id();
        $data["updated_by"] = get_current_user_id();
        $project = new Project($data);
        $id = $this->repository->insert($project);
        if (false === $id) {
            return new WP_Error(
                "projectpress_project_create_failed",
                __("The project could not be created.", "projectpress"),
            );
        }
        do_action("projectpress_project_created", $id, $project);
        return $this->repository->find($id);
    }

    /**
     * Update an existing project.
     *
     * @param Project $project Project to update.
     * @param array   $input   Project data.
     * @return Project|WP_Error
     * @since 1.0.0
     */
    public function update(Project $project, array $input) {
        if (!$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_edit_forbidden",
                __("You do not have permission to edit this project.", "projectpress"),
            );
        }
        $data = $this->validator->validate($input, true);
        if (is_wp_error($data)) {
            return $data;
        }
        $updated = new Project(array_merge($project->to_array(), $data, ["updated_by" => get_current_user_id()]));
        if (!$this->repository->update($updated)) {
            return new WP_Error(
                "projectpress_project_update_failed",
                __("The project could not be updated.", "projectpress"),
            );
        }
        do_action("projectpress_project_updated", $updated);
        return $this->repository->find($updated->get_id());
    }

    /**
     * Archive a project.
     *
     * @param Project $project Project to archive.
     * @return Project|WP_Error
     * @since 1.0.0
     */
    public function archive(Project $project) {
        if (!$this->permissions->can_archive($project)) {
            return new WP_Error(
                "projectpress_archive_forbidden",
                __("You do not have permission to archive this project.", "projectpress"),
            );
        }
        if ("archived" === $project->get_status()) {
            return $project;
        }
        $result = $this->update($project, ["status" => "archived"]);
        if (!is_wp_error($result)) {
            do_action("projectpress_project_archived", $project->get_id(), $project);
        }
        return $result;
    }

    /**
     * Restore an archived project.
     *
     * @param Project $project Project to restore.
     * @return Project|WP_Error
     * @since 1.0.0
     */
    public function restore(Project $project) {
        if (!$this->permissions->can_restore($project)) {
            return new WP_Error(
                "projectpress_restore_forbidden",
                __("You do not have permission to restore this project.", "projectpress"),
            );
        }
        if ("archived" !== $project->get_status()) {
            return $project;
        }
        $result = $this->update($project, ["status" => Defaults::PROJECT_STATUS_DEFAULT]);
        if (!is_wp_error($result)) {
            do_action("projectpress_project_restored", $project->get_id(), $result);
        }
        return $result;
    }

    /**
     * Permanently delete a project.
     *
     * @param Project $project Project to delete.
     * @return true|WP_Error
     * @since 1.0.0
     */
    public function delete(Project $project) {
        if (!$this->permissions->can_delete($project)) {
            return new WP_Error(
                "projectpress_delete_forbidden",
                __("You do not have permission to permanently delete this project.", "projectpress"),
            );
        }
        if ("archived" !== $project->get_status()) {
            return new WP_Error(
                "projectpress_delete_requires_archive",
                __("A project must be archived before it can be permanently deleted.", "projectpress"),
            );
        }
        $content_result = $this->delete_wordpress_content($project->get_id());
        if (is_wp_error($content_result)) {
            return $content_result;
        }
        if (!$this->repository->delete($project->get_id())) {
            return new WP_Error(
                "projectpress_project_delete_failed",
                __("The project could not be permanently deleted.", "projectpress"),
            );
        }
        do_action("projectpress_project_deleted", $project->get_id(), $project);
        return true;
    }

    /**
     * Permanently delete project-owned WordPress content.
     *
     * @param int $project_id Project ID.
     * @return true|WP_Error
     * @since 1.0.0
     */
    private function delete_wordpress_content(int $project_id) {
        $post_ids = get_posts([
            "post_type" => ["pp_note", "pp_document", "pp_update", "pp_discussion"],
            "post_status" => "any",
            "posts_per_page" => -1,
            "fields" => "ids",
            "meta_key" => "_pp_project_id",
            "meta_value" => absint($project_id),
        ]);
        foreach ($post_ids as $post_id) {
            if (false === wp_delete_post(absint($post_id), true)) {
                return new WP_Error(
                    "projectpress_project_content_delete_failed",
                    __("Project content could not be permanently deleted.", "projectpress"),
                );
            }
        }
        $attachment_ids = get_posts([
            "post_type" => "attachment",
            "post_status" => "inherit",
            "posts_per_page" => -1,
            "fields" => "ids",
            "meta_key" => "_pp_project_id",
            "meta_value" => absint($project_id),
        ]);
        foreach ($attachment_ids as $attachment_id) {
            foreach (
                ["_pp_project_id", "_pp_object_type", "_pp_object_id", "_pp_file_visibility", "_pp_file_description"]
                as $key
            ) {
                delete_post_meta(absint($attachment_id), $key);
            }
        }
        return true;
    }
}
