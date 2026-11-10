<?php
/**
 * TinyMCE Editor Plugin Assets
 *
 * @package ProjectPress
 * @subpackage Plugins\TinyMCE\Assets
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Plugins\TinyMCE\Assets;

use ProjectPress\Includes\Plugins\TinyMCE\Includes\Settings\Settings;
use ProjectPress\Includes\Functions\Helpers\ImageHelper;
use ProjectPress\Includes\Functions\Helpers\LoaderHelper;

final class Assets {
	/**
	 * The loader helper instance.
	 *
	 * @var LoaderHelper The loader helper instance.
	 */
	private LoaderHelper $loader;
	/**
	 * Constructor for the TinyMCE plugin assets.
	 *
	 * @param LoaderHelper|null $loader The loader helper instance.
	 */
	public function __construct( ?LoaderHelper $loader = null ) {
		$this->loader = $loader ?? new LoaderHelper();
	}

	/**
	 * Registers the TinyMCE plugin assets with the core assets manager.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->loader->register_component(
			$this,
			array(
				array(
					'type'          => 'filter',
					'hook'          => 'projectpress_admin_assets',
					'callback'      => 'register_admin_assets',
					'accepted_args' => 2,
				),
			)
		)->run();
	}
	/**
	 * Registers the admin assets for the TinyMCE plugin.
	 *
	 * @param array  $assets The current assets.
	 * @param string $context The context (e.g., 'frontend', 'admin').
	 * @return array The updated assets with TinyMCE assets included.
	 */
	public function register_admin_assets( $assets = array(), string $context = 'admin' ): array {
		if ( ! is_array( $assets ) ) {
			$assets = array();
		}

		if ( 'admin' !== strtolower( $context ) && 'frontend' !== strtolower( $context ) ) {
			return $assets;
		}

		$base_url = PROJECTPRESS_URL . 'src/Includes/Plugins/TinyMCE/Assets/tinymce/';

		$assets['styles'][]  = array(
			'handle' => 'projectpress-tinymce-skin',
			'src'    => $base_url . 'skins/ui/' . Settings::ui_skin() . '/skin.min.css',
		);
		$assets['scripts'][] = array(
			'handle'    => 'projectpress-tinymce',
			'src'       => $base_url . 'tinymce.min.js',
			'in_footer' => true,
		);
		$assets['scripts'][] = array(
			'handle'    => 'projectpress-tinymce-boot',
			'src'       => PROJECTPRESS_URL . 'src/Includes/Plugins/TinyMCE/Assets/js/tinymce.js',
			'deps'      => array( 'projectpress-tinymce' ),
			'in_footer' => true,
			'localize'  => array(
				'object_name' => 'projectpressTinyMCE',
				'data'        => array(
					'mediaTitle'   => __( 'Insert media', 'projectpress' ),
					'mediaButton'  => __( 'Insert into editor', 'projectpress' ),
					'mediaTooltip' => __( 'Insert media', 'projectpress' ),
				),
			),
		);

		$assets['enqueue_media'] = true;

		return $assets;
	}
	/**
	 * Get an image asset URL from the core Images directory.
	 *
	 * @param string $file The image path relative to Assets/images.
	 * @return string The image URL, or an empty string when the path is invalid.
	 */
	public static function get_image( string $file ): string {
		return ImageHelper::get_image_url( 'projectpress-tinymce', $file );
	}
}



