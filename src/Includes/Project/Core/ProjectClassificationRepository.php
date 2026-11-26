<?php
/**
 * Project taxonomy classification repository.
 *
 * @package ProjectPress
 * @subpackage ProjectPress\Includes\Project\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Core;

use ProjectPress\Includes\Core\WP\Database;
use ProjectPress\Includes\Pages\Taxonomies;
use WP_Error;

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Reads and mutates allowed project classifications.
 *
 * @since 1.0.0
 */
final class ProjectClassificationRepository {
    /**
     * Allowed project taxonomies.
     *
     * @return array
     * @since 1.0.0
     */
    public static function allowed_taxonomies(): array {
        return ["pp_project_type", "pp_project_category", "pp_project_tag", "pp_project_label"];
    }

    /**
     * terms.
     *
     * @param string $taxonomy taxonomy parameter.
     * @return array terms result.
     * @since 1.0.0
     */
    public function terms(string $taxonomy): array {
        if (!in_array($taxonomy, self::allowed_taxonomies(), true)) {
            return [];
        }
        $terms = get_terms([
            "taxonomy" => $taxonomy,
            "hide_empty" => false,
            "meta_key" => "_pp_sort_order",
            "orderby" => "meta_value_num",
            "order" => "ASC",
        ]);
        return is_wp_error($terms) ? [] : $terms;
    }

    /**
     * assigned_term_ids.
     *
     * @param int $project_id project ID parameter.
     * @param string $taxonomy taxonomy parameter.
     * @return array assigned term IDs result.
     * @since 1.0.0
     */
    public function assigned_term_ids(int $project_id, string $taxonomy): array {
        if ($project_id < 1 || !in_array($taxonomy, self::allowed_taxonomies(), true)) {
            return [];
        }
        global $wpdb;
        $rows = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT term_id FROM " .
                    Database::table_name("project_term_relationships") .
                    " WHERE project_id = %d AND taxonomy = %s",
                $project_id,
                $taxonomy,
            ),
        );
        return array_values(array_unique(array_map("absint", (array) $rows)));
    }

    /**
     * create_term.
     *
     * @param string $taxonomy taxonomy parameter.
     * @param string $name name parameter.
     * @param array $data data parameter.
     * @return \WP_Term|WP_Error create_term result.
     * @since 1.0.0
     */
    public function create_term(string $taxonomy, string $name, array $data = []) {
        if (!in_array($taxonomy, self::allowed_taxonomies(), true)) {
            return new WP_Error("projectpress_invalid_taxonomy", __("Invalid project classification.", "projectpress"));
        }
        $result = wp_insert_term($name, $taxonomy, [
            "slug" => sanitize_title($data["slug"] ?? $name),
            "description" => sanitize_textarea_field($data["description"] ?? ""),
            "parent" => absint($data["parent"] ?? 0),
        ]);
        if (is_wp_error($result)) {
            return $result;
        }
        $id = (int) $result["term_id"];
        update_term_meta($id, "_pp_colour", sanitize_hex_color($data["colour"] ?? "") ?: "");
        update_term_meta($id, "_pp_icon", sanitize_key($data["icon"] ?? ""));
        update_term_meta($id, "_pp_sort_order", absint($data["sort_order"] ?? 0));
        update_term_meta($id, "_pp_enabled", empty($data["enabled"]) ? 0 : 1);
        return get_term($id, $taxonomy);
    }

    /**
     * assign.
     *
     * @param int $project_id project ID parameter.
     * @param string $taxonomy taxonomy parameter.
     * @param array $term_ids term IDs parameter.
     * @return bool assign result.
     * @since 1.0.0
     */
    public function assign(int $project_id, string $taxonomy, array $term_ids): bool {
        if ($project_id < 1 || !in_array($taxonomy, self::allowed_taxonomies(), true)) {
            return false;
        }
        global $wpdb;
        $table = Database::table_name("project_term_relationships");
        $wpdb->delete($table, ["project_id" => $project_id, "taxonomy" => $taxonomy], ["%d", "%s"]);
        foreach (array_unique(array_map("absint", $term_ids)) as $term_id) {
            $term = get_term($term_id, $taxonomy);
            if (!$term || is_wp_error($term)) {
                continue;
            }
            $wpdb->insert(
                $table,
                [
                    "project_id" => $project_id,
                    "term_id" => $term_id,
                    "taxonomy" => $taxonomy,
                    "assigned_by" => get_current_user_id(),
                    "assigned_at" => current_time("mysql", true),
                ],
                ["%d", "%d", "%s", "%d", "%s"],
            );
        }
        return true;
    }
}
