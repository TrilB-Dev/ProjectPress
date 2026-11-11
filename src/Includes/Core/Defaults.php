<?php
/**
 * Defaults class for defining plugin default values.
 *
 * @package ProjectPress
 * @subpackage Includes\Core
 * @since 1.0.0
 */
namespace ProjectPress\Includes\Core;

use ProjectPress\Includes\Functions\Helpers\DBHelper;
use ProjectPress\Includes\Functions\Helpers\LoggerHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Defaults {
    /**
     * Constructor for the Defaults class.
     *
     * @since 1.0.0
     */
	public function __construct() {
		// Initialize default values here.
	}
    /**
     * Install all default values for the plugin.
     *
     * @return void
     * @since 1.0.0
     */
	public static function install_defaults(): void {
	}
    /**
     * Reset the default groups by deleting and recreating the groups table.
     *
     * @return bool True on success, false on failure.
     * @since 1.0.0
     */

}

