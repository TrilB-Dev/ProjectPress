<?php
/**
 * AttachmentRepository for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Collaboration;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * AttachmentRepository implementation.
 *
 * @since 1.0.0
 */
final class AttachmentRepository {
    /**
     * for_project.
     *
     * @param int $project_id project_id parameter.
     *
     * @return array for_project result.
     *
     * @since 1.0.0
     */
    public function for_project(int $project_id): array {
        $posts = get_posts([
            "post_type" => "attachment",
            "post_status" => "inherit",
            "posts_per_page" => -1,
            "orderby" => "date",
            "order" => "DESC",
            "meta_key" => "_pp_project_id",
            "meta_value" => absint($project_id),
        ]);
        return array_map([$this, "hydrate"], $posts);
    }
    /**
     * find.
     *
     * @param int $attachment_id attachment_id parameter.
     *
     * @return ?Attachment find result.
     *
     * @since 1.0.0
     */
    public function find(int $attachment_id): ?Attachment {
        $post = get_post(absint($attachment_id));
        if (!$post || "attachment" !== $post->post_type) {
            return null;
        }
        return $this->hydrate($post);
    }
    /**
     * attach.
     *
     * @param int $project_id project_id parameter.
     * @param int $attachment_id attachment_id parameter.
     * @param string $label label parameter.
     *
     * @return mixed attach result.
     *
     * @since 1.0.0
     */
    public function attach(int $project_id, int $attachment_id, string $label = "") {
        if ("attachment" !== get_post_type($attachment_id)) {
            return false;
        }
        update_post_meta($attachment_id, "_pp_project_id", absint($project_id));
        update_post_meta($attachment_id, "_pp_object_type", "project");
        update_post_meta($attachment_id, "_pp_object_id", absint($project_id));
        if ("" !== $label) {
            update_post_meta($attachment_id, "_pp_file_description", sanitize_text_field($label));
        }
        return $this->find($attachment_id);
    }
    /**
     * detach.
     *
     * @param int $project_id project_id parameter.
     * @param int $attachment_id attachment_id parameter.
     *
     * @return bool detach result.
     *
     * @since 1.0.0
     */
    public function detach(int $project_id, int $attachment_id): bool {
        if (absint(get_post_meta($attachment_id, "_pp_project_id", true)) !== absint($project_id)) {
            return false;
        }
        foreach (
            ["_pp_project_id", "_pp_object_type", "_pp_object_id", "_pp_file_visibility", "_pp_file_description"]
            as $key
        ) {
            delete_post_meta($attachment_id, $key);
        }
        return true;
    }
    /**
     * hydrate.
     *
     * @param mixed $post post parameter.
     *
     * @return Attachment hydrate result.
     *
     * @since 1.0.0
     */
    private function hydrate($post): Attachment {
        return new Attachment([
            "id" => (int) $post->ID,
            "project_id" => absint(get_post_meta($post->ID, "_pp_project_id", true)),
            "attachment_id" => (int) $post->ID,
            "file_label" => (string) (get_post_meta($post->ID, "_pp_file_description", true) ?: $post->post_title),
        ]);
    }
}
