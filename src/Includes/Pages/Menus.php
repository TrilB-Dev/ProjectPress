<?php
/**
 * Menus class for registering plugin menus.
 *
 * @package ProjectPress
 * @subpackage Includes\Pages
 * @since 1.0.0
 */
namespace ProjectPress\Includes\Pages;

use ProjectPress\Includes\Core\Menus as CoreMenus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Menus {
	/**
	 * Register all page-layer menu locations.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register(): void {
		self::primary_navigation();
	}

	/**
	 * Register the built-in primary navigation menu location.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function primary_navigation(): bool {
		return CoreMenus::get_instance()->register_location( 
            'primary', 
            __( 'Primary Navigation', 'projectpress' ), 
            true 
        ) instanceof CoreMenus;
	}
}

