<?php
/**
 * ContentRepository for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Collaboration;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * ContentRepository implementation.
 *
 * @since 1.0.0
 */
final class ContentRepository {
    /**
     * for_project.
     *
     * @param string $type type parameter.
     * @param int $project_id project_id parameter.
     *
     * @return array for_project result.
     *
     * @since 1.0.0
     */
    public function for_project(string $type, int $project_id): array {
        return get_posts([
            "post_type" => $type,
            "post_status" => ["publish", "draft", "private"],
            "posts_per_page" => -1,
            "orderby" => "date",
            "order" => "DESC",
            "meta_key" => "_pp_project_id",
            "meta_value" => absint($project_id),
        ]);
    }
    /**
     * find.
     *
     * @param string $type type parameter.
     * @param int $id id parameter.
     *
     * @return mixed find result.
     *
     * @since 1.0.0
     */
    public function find(string $type, int $id) {
        $post = get_post(absint($id));
        return $post && $type === $post->post_type ? $post : null;
    }
    /**
     * create.
     *
     * @param string $type type parameter.
     * @param int $project_id project_id parameter.
     * @param array $data data parameter.
     *
     * @return mixed create result.
     *
     * @since 1.0.0
     */
    public function create(string $type, int $project_id, array $data) {
        $id = wp_insert_post(
            [
                "post_type" => $type,
                "post_status" => "publish",
                "post_title" => sanitize_text_field($data["title"] ?? ""),
                "post_content" => wp_kses_post($data["content"] ?? ""),
                "post_author" => get_current_user_id(),
            ],
            true,
        );
        if (is_wp_error($id)) {
            return $id;
        }
        update_post_meta($id, "_pp_project_id", absint($project_id));
        if ("pp_discussion" === $type) {
            update_post_meta($id, "_pp_discussion_status", "open");
        }
        return get_post($id);
    }
    /**
     * delete.
     *
     * @param string $type type parameter.
     * @param int $project_id project_id parameter.
     * @param int $id id parameter.
     *
     * @return bool delete result.
     *
     * @since 1.0.0
     */
    public function delete(string $type, int $project_id, int $id): bool {
        $post = $this->find($type, $id);
        return $post &&
            absint(get_post_meta($id, "_pp_project_id", true)) === absint($project_id) &&
            false !== wp_delete_post($id, true);
    }
    /**
     * comments.
     *
     * @param int $discussion_id discussion_id parameter.
     *
     * @return array comments result.
     *
     * @since 1.0.0
     */
    public function comments(int $discussion_id): array {
        return get_comments([
            "post_id" => absint($discussion_id),
            "status" => "approve",
            "order" => "ASC",
        ]);
    }
    /**
     * add_comment.
     *
     * @param int $project_id project_id parameter.
     * @param int $discussion_id discussion_id parameter.
     * @param string $content content parameter.
     *
     * @return mixed add_comment result.
     *
     * @since 1.0.0
     */
    public function add_comment(int $project_id, int $discussion_id, string $content) {
        $post = $this->find("pp_discussion", $discussion_id);
        if (!$post || absint(get_post_meta($discussion_id, "_pp_project_id", true)) !== absint($project_id)) {
            return false;
        }
        $user = wp_get_current_user();

        return wp_insert_comment([
            "comment_post_ID" => $discussion_id,
            "comment_content" => wp_kses_post($content),
            "user_id" => $user->ID,
            "comment_author" => $user->display_name,
            "comment_author_email" => $user->user_email,
            "comment_approved" => 1,
        ]);
    }
}
