<?php
/**
 * Taxonomies class for registering plugin taxonomies.
 *
 * @package ProjectPress
 * @subpackage Includes\Pages
 * @since 1.0.0
 */
namespace ProjectPress\Includes\Pages;

use ProjectPress\Includes\Core\Taxonomy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Taxonomies {
	/**
	 * Register the page-layer taxonomies.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register(): void {
		self::content_category_taxonomy();
		self::content_tag_taxonomy();
		self::document_type_taxonomy();
		self::category_taxonomy();
		self::tag_taxonomy();
	}

	/**
	 * Register the project content category taxonomy.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function content_category_taxonomy(): bool {
		if ( taxonomy_exists( 'pp_content_category' ) ) {
			return false;
		}

		return Taxonomy::create(
			'pp_content_category',
			array(
				'label'             => __( 'Content Categories', 'projectpress' ),
				'description'       => __( 'Categories used to organize project content.', 'projectpress' ),
				'post_type'         => array( 'pp_note', 'pp_document', 'pp_update', 'pp_discussion' ),
				'hierarchical'      => true,
				'public'            => false,
				'show_ui'           => true,
				'show_in_menu'      => false,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'pp-content-category' ),
				'meta'              => self::taxonomy_meta_fields(),
			)
		);
	}

	/**
	 * Register the project content tag taxonomy.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function content_tag_taxonomy(): bool {
		if ( taxonomy_exists( 'pp_content_tag' ) ) {
			return false;
		}

		return Taxonomy::create(
			'pp_content_tag',
			array(
				'label'             => __( 'Content Tags', 'projectpress' ),
				'description'       => __( 'Tags used to label project content.', 'projectpress' ),
				'post_type'         => array( 'pp_note', 'pp_document', 'pp_update', 'pp_discussion' ),
				'hierarchical'      => false,
				'public'            => false,
				'show_ui'           => true,
				'show_in_menu'      => false,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'pp-content-tag' ),
				'meta'              => self::taxonomy_meta_fields(),
			)
		);
	}

	/**
	 * Register the project document type taxonomy.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function document_type_taxonomy(): bool {
		if ( taxonomy_exists( 'pp_document_type' ) ) {
			return false;
		}

		return Taxonomy::create(
			'pp_document_type',
			array(
				'label'             => __( 'Document Types', 'projectpress' ),
				'description'       => __( 'Document classifications used across project records.', 'projectpress' ),
				'post_type'         => array( 'pp_document' ),
				'hierarchical'      => true,
				'public'            => false,
				'show_ui'           => true,
				'show_in_menu'      => false,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'pp-document-type' ),
				'meta'              => self::taxonomy_meta_fields(),
			)
		);
	}

	/**
	 * Register the legacy project category taxonomy.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function category_taxonomy(): bool {
		if ( taxonomy_exists( 'projectpress_category' ) ) {
			return false;
		}

		return Taxonomy::create(
			'projectpress_category',
			array(
				'label'             => __( 'Categories', 'projectpress' ),
				'description'       => __( 'Categories used to organize projects.', 'projectpress' ),
				'post_type'         => array( 'projectpress_projects' ),
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_in_menu'      => false,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'project-category' ),
			)
		);
	}

	/**
	 * Register the legacy project tag taxonomy.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function tag_taxonomy(): bool {
		if ( taxonomy_exists( 'projectpress_tag' ) ) {
			return false;
		}

		return Taxonomy::create(
			'projectpress_tag',
			array(
				'label'             => __( 'Tags', 'projectpress' ),
				'description'       => __( 'Tags used to describe projects.', 'projectpress' ),
				'post_type'         => array( 'projectpress_projects' ),
				'hierarchical'      => false,
				'public'            => true,
				'show_ui'           => true,
				'show_in_menu'      => false,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'project-tag' ),
			)
		);
	}

	/**
	 * Define the term metadata fields shared by content taxonomies.
	 *
	 * @return array<string, array<string, mixed>>
	 * @since 1.0.0
	 */
	private static function taxonomy_meta_fields(): array {
		return array(
			'_pp_colour' => array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => true,
			),
			'_pp_icon' => array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => true,
			),
			'_pp_sort_order' => array(
				'type'         => 'integer',
				'single'       => true,
				'show_in_rest' => true,
			),
			'_pp_enabled' => array(
				'type'         => 'boolean',
				'single'       => true,
				'show_in_rest' => true,
			),
		);
	}
}
