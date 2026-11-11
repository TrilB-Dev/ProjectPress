<?php
/**
 * PostTypes class for registering plugin post types.
 *
 * @package ProjectPress
 * @subpackage Includes\Pages
 * @since 1.0.0
 */
namespace ProjectPress\Includes\Pages;

use ProjectPress\Includes\Core\PostType;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class PostTypes {
	/**
	 * Register all page-layer post types.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register(): void {
		self::projects_posttype();
		self::content_posttypes();
		self::register_attachment_metadata();
	}

	/**
	 * Register the core ProjectPress project post type.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function projects_posttype(): bool {
		return PostType::create(
			'projectpress_projects',
			array(
				'label'           => __( 'Projects', 'projectpress' ),
				'description'     => __( 'Projects managed by ProjectPress.', 'projectpress' ),
				'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
				'taxonomies'      => array( 'projectpress_category', 'projectpress_tag' ),
				'public'          => true,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_position'   => 20,
				'menu_icon'       => 'dashicons-archive',
				'has_archive'     => true,
				'show_in_rest'    => true,
				'rewrite'         => array( 'slug' => 'projects' ),
				'capability_type' => 'post',
				'hierarchical'    => true,
				'map_meta_cap'    => true,
			)
		);
	}

	/**
	 * Register the project content post types.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function content_posttypes(): void {
		$post_types = array(
			'pp_note' => array(
				'label'        => __( 'Notes', 'projectpress' ),
				'description'  => __( 'Project notes and annotations.', 'projectpress' ),
				'supports'     => array( 'title', 'editor', 'author', 'revisions' ),
				'taxonomies'   => array( 'pp_content_category', 'pp_content_tag' ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => false,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-clipboard',
				'hierarchical' => false,
				'has_archive'  => false,
				'meta'         => self::content_meta_fields(
					array(
						'_pp_project_id',
						'_pp_visibility',
						'_pp_pinned',
						'_pp_featured',
						'_pp_sort_order',
						'_pp_note_type',
						'_pp_note_date',
						'_pp_milestone_id',
						'_pp_task_id',
					)
				),
			),
			'pp_document' => array(
				'label'        => __( 'Documents', 'projectpress' ),
				'description'  => __( 'Project documents and records.', 'projectpress' ),
				'supports'     => array( 'title', 'editor', 'author', 'revisions' ),
				'taxonomies'   => array( 'pp_content_category', 'pp_content_tag', 'pp_document_type' ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => false,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-media-document',
				'hierarchical' => false,
				'has_archive'  => false,
				'meta'         => self::content_meta_fields(
					array(
						'_pp_project_id',
						'_pp_visibility',
						'_pp_pinned',
						'_pp_featured',
						'_pp_sort_order',
						'_pp_document_status',
						'_pp_document_version',
						'_pp_document_reference',
						'_pp_approved_by',
						'_pp_approved_at',
						'_pp_review_date',
					)
				),
			),
			'pp_update' => array(
				'label'        => __( 'Updates', 'projectpress' ),
				'description'  => __( 'Project status updates and progress reports.', 'projectpress' ),
				'supports'     => array( 'title', 'editor', 'author', 'revisions' ),
				'taxonomies'   => array( 'pp_content_category', 'pp_content_tag' ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => false,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-megaphone',
				'hierarchical' => false,
				'has_archive'  => false,
				'meta'         => self::content_meta_fields(
					array(
						'_pp_project_id',
						'_pp_visibility',
						'_pp_pinned',
						'_pp_featured',
						'_pp_sort_order',
						'_pp_update_type',
						'_pp_update_date',
						'_pp_progress',
					)
				),
			),
			'pp_discussion' => array(
				'label'        => __( 'Discussions', 'projectpress' ),
				'description'  => __( 'Project discussions and decision threads.', 'projectpress' ),
				'supports'     => array( 'title', 'editor', 'author', 'revisions' ),
				'taxonomies'   => array( 'pp_content_category', 'pp_content_tag' ),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => false,
				'show_in_rest' => true,
				'menu_icon'    => 'dashicons-format-chat',
				'hierarchical' => false,
				'has_archive'  => false,
				'meta'         => self::content_meta_fields(
					array(
						'_pp_project_id',
						'_pp_visibility',
						'_pp_pinned',
						'_pp_featured',
						'_pp_sort_order',
						'_pp_discussion_status',
						'_pp_discussion_type',
						'_pp_milestone_id',
						'_pp_task_id',
						'_pp_resolved_by',
						'_pp_resolved_at',
					)
				),
			),
		);

		foreach ( $post_types as $slug => $config ) {
			PostType::create( $slug, $config );
		}
	}

	/**
	 * Build a consistent metadata map for project content post types.
	 *
	 * @param array<int, string> $meta_keys Meta keys to register.
	 * @return array<string, array<string, mixed>>
	 * @since 1.0.0
	 */
	private static function content_meta_fields( array $meta_keys ): array {
		$meta = array();

		foreach ( $meta_keys as $meta_key ) {
			$meta[ $meta_key ] = array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => static function ( $value ) {
					return is_scalar( $value ) ? (string) $value : '';
				},
			);
		}

		return $meta;
	}

	/**
	 * Register shared attachment metadata fields.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register_attachment_metadata(): void {
		$meta_keys = array(
			'_pp_project_id',
			'_pp_object_type',
			'_pp_object_id',
			'_pp_file_visibility',
			'_pp_file_description',
		);

		foreach ( $meta_keys as $meta_key ) {
			register_post_meta(
				'attachment',
				$meta_key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => static function ( $value ) {
						return is_scalar( $value ) ? (string) $value : '';
					},
				)
			);
		}
	}
}

