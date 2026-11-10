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
	}

	/**
	 * Register the core ProjectPress mod post type.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function projects_posttype(): bool {
		return PostType::create(
			'projectpress_projects',
			array(
				'label'           => __( 'Projects', 'projectpress' ),
				'description'     => __( 'Mod entries managed by ProjectPress.', 'projectpress' ),
				'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
				'taxonomies'      => array( 'projectpress_category', 'projectpress_tag' ),
				'public'          => true,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'menu_position'   => 20,
				'menu_icon'       => 'dashicons-archive',
				'has_archive'     => true,
				'show_in_rest'    => true,
				'rewrite'         => array( 'slug' => 'project' ),
				'capability_type' => 'post',
				'hierarchical'    => true,
				'map_meta_cap'    => true,
			)
		);
	}
}

