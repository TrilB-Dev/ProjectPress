<?php
/**
 * ProjectPress taxonomy definitions.
 *
 * @since 1.0.0
 */
namespace ProjectPress\Includes\Pages;

use ProjectPress\Includes\Core\Taxonomy;

if (!defined("ABSPATH")) {
    exit();
}

final class Taxonomies {
    /**
     * Register taxonomies.
     *
     * @since 1.0.0
     */
    public static function register(): void {
        self::create("pp_content_category", __("Content Categories", "projectpress"), true, [
            "pp_note",
            "pp_document",
            "pp_update",
            "pp_discussion",
        ]);
        self::create("pp_content_tag", __("Content Tags", "projectpress"), false, [
            "pp_note",
            "pp_document",
            "pp_update",
            "pp_discussion",
        ]);
        self::create("pp_document_type", __("Document Types", "projectpress"), true, ["pp_document"]);
        self::create("pp_project_type", __("Project Types", "projectpress"), true);
        self::create("pp_project_category", __("Project Categories", "projectpress"), true);
        self::create("pp_project_tag", __("Project Tags", "projectpress"), false);
        self::create("pp_project_label", __("Project Labels", "projectpress"), false);
    }
    /**
     * Create a taxonomy if it doesn't exist.
     *
     * @param string $taxonomy
     * @param string $label
     * @param bool $hierarchical
     * @param array $post_types
     * @return bool
     */
    private static function create(string $taxonomy, string $label, bool $hierarchical, array $post_types = []): bool {
        if (taxonomy_exists($taxonomy)) {
            return true;
        }
        return Taxonomy::create($taxonomy, [
            "label" => $label,
            "post_type" => $post_types,
            "hierarchical" => $hierarchical,
            "public" => false,
            "show_ui" => false,
            "show_in_menu" => false,
            "show_admin_column" => false,
            "show_in_rest" => true,
            "rewrite" => false,
            "meta" => self::taxonomy_meta_fields(),
        ]);
    }
    /**
     * Get the meta fields for taxonomies.
     *
     * @return array
     */
    private static function taxonomy_meta_fields(): array {
        return [
            "_pp_colour" => [
                "type" => "string",
                "single" => true,
                "show_in_rest" => true,
                "sanitize_callback" => "sanitize_hex_color",
            ],
            "_pp_icon" => [
                "type" => "string",
                "single" => true,
                "show_in_rest" => true,
                "sanitize_callback" => "sanitize_key",
            ],
            "_pp_sort_order" => [
                "type" => "integer",
                "single" => true,
                "show_in_rest" => true,
                "sanitize_callback" => "absint",
            ],
            "_pp_enabled" => [
                "type" => "boolean",
                "single" => true,
                "show_in_rest" => true,
                "sanitize_callback" => "rest_sanitize_boolean",
            ],
        ];
    }
}
