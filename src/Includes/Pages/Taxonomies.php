<?php
/** ProjectPress taxonomy definitions. */
namespace ProjectPress\Includes\Pages;

use ProjectPress\Includes\Core\Taxonomy;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Taxonomies {
	public const PROJECT_TYPE = 'pp_project_type';
	public const PROJECT_CATEGORY = 'pp_project_category';
	public const PROJECT_TAG = 'pp_project_tag';
	public const PROJECT_LABEL = 'pp_project_label';

	public static function register(): void {
		self::create( 'pp_content_category', __( 'Content Categories', 'projectpress' ), true, array( 'pp_note', 'pp_document', 'pp_update', 'pp_discussion' ) );
		self::create( 'pp_content_tag', __( 'Content Tags', 'projectpress' ), false, array( 'pp_note', 'pp_document', 'pp_update', 'pp_discussion' ) );
		self::create( 'pp_document_type', __( 'Document Types', 'projectpress' ), true, array( 'pp_document' ) );
		self::create( self::PROJECT_TYPE, __( 'Project Types', 'projectpress' ), true );
		self::create( self::PROJECT_CATEGORY, __( 'Project Categories', 'projectpress' ), true );
		self::create( self::PROJECT_TAG, __( 'Project Tags', 'projectpress' ), false );
		self::create( self::PROJECT_LABEL, __( 'Project Labels', 'projectpress' ), false );
	}

	private static function create( string $taxonomy, string $label, bool $hierarchical, array $post_types = array() ): bool {
		if ( taxonomy_exists( $taxonomy ) ) { return true; }
		return Taxonomy::create( $taxonomy, array(
			'label' => $label,
			'post_type' => $post_types,
			'hierarchical' => $hierarchical,
			'public' => false,
			'show_ui' => false,
			'show_in_menu' => false,
			'show_admin_column' => false,
			'show_in_rest' => true,
			'rewrite' => false,
			'meta' => self::taxonomy_meta_fields(),
		) );
	}

	private static function taxonomy_meta_fields(): array {
		return array(
			'_pp_colour' => array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'sanitize_hex_color' ),
			'_pp_icon' => array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'sanitize_key' ),
			'_pp_sort_order' => array( 'type' => 'integer', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'absint' ),
			'_pp_enabled' => array( 'type' => 'boolean', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'rest_sanitize_boolean' ),
		);
	}
}
