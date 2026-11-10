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
		self::category_taxonomy();
		self::tag_taxonomy();
	}

	/**
	 * Register the mod group taxonomy.
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
				'description'       => __( 'Categories used to organize mods.', 'projectpress' ),
				'post_type'       	=> array( 'projectpress_posttype' ),
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_in_menu'      => false,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'category' ),
			)
		);
	}

	/**
	 * Register the mod tag taxonomy.
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
				'description'       => __( 'Tags used to describe mods.', 'projectpress' ),
				'post_type'       => array( 'projectpress_posttype' ),
				'hierarchical'      => false,
				'public'            => true,
				'show_ui'           => true,
				'show_in_menu'      => false,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'tag' ),
			)
		);
	}
}
