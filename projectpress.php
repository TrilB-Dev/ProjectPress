<?php

/**
 * ProjectPress - A WordPress Plugin
 *
 * This is the main plugin file for the ProjectPress WordPress plugin. It contains the plugin metadata and initializes the plugin by including necessary files and setting up activation and deactivation hooks.
 *
 * Plugin Name:       ProjectPress
 * Plugin URI:        https://trilb.dev/collection/web-extension/wordpress/projectpress
 * Description:       ProjectPress is a WordPress plugin.
 * Author:            MrTrilB
 * Author URI:        https://trilb.dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       projectpress
 * Version:           1.0.0
 * Domain Path:       src/languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PROJECTPRESS_VERSION', '1.0.0' );
define( 'PROJECTPRESS_NAME', 'projectpress' );
define( 'PROJECTPRESS_DEFAULT_LANGUAGE', 'en_GB' );
define( 'PROJECTPRESS_FILE', __FILE__ );
define( 'PROJECTPRESS_DIR', plugin_dir_path( __FILE__ ) );
define( 'PROJECTPRESS_URL', plugin_dir_url( __FILE__ ) );
define( 'PROJECTPRESS_BASENAME', plugin_basename( __FILE__ ) );
define( 'PROJECTPRESS_ROOT', PROJECTPRESS_DIR );
define( 'PROJECTPRESS_ROOT_URL', PROJECTPRESS_URL );
define( 'PROJECTPRESS_API', PROJECTPRESS_DIR . 'src/API' );
define( 'PROJECTPRESS_ASSETS', PROJECTPRESS_DIR . 'src/Assets' );
define( 'PROJECTPRESS_ASSETS_URL', PROJECTPRESS_URL . 'src/Assets' );
define( 'PROJECTPRESS_ADMIN', PROJECTPRESS_DIR . 'src/Admin' );
define( 'PROJECTPRESS_ADMIN_URL', PROJECTPRESS_URL . 'src/Admin' );
define( 'PROJECTPRESS_LANGUAGES', PROJECTPRESS_DIR . 'src/languages' );
define( 'PROJECTPRESS_INCLUDES', PROJECTPRESS_DIR . 'src/Includes' );
define( 'PROJECTPRESS_CORE', PROJECTPRESS_INCLUDES . '/Core' );
define( 'PROJECTPRESS_SETTINGS', PROJECTPRESS_INCLUDES . '/Settings' );
define( 'PROJECTPRESS_PLUGINS', PROJECTPRESS_INCLUDES . '/Plugins' );
define( 'PROJECTPRESS_PLUGINS_URL', PROJECTPRESS_URL . 'src/Includes/Plugins' );

$projectpress_autoloader = PROJECTPRESS_DIR . 'vendor/autoload.php';
if ( is_readable( $projectpress_autoloader ) ) {
	require_once $projectpress_autoloader;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in Includes/class-projectpress-activator.php
 */
function activate_projectpress() {
	\ProjectPress\Includes\Core\WP\Activator::activate();
}

register_activation_hook( __FILE__, 'activate_projectpress' );
/**
 * The code that runs during plugin deactivation.
 * This action is documented in Includes/class-projectpress-deactivator.php
 */
function deactivate_projectpress() {
	\ProjectPress\Includes\Core\WP\Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_projectpress' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once PROJECTPRESS_DIR . 'src/Plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_projectpress() {

	$plugin = new \ProjectPress\Plugin( PROJECTPRESS_FILE, PROJECTPRESS_NAME, PROJECTPRESS_VERSION );
	$plugin->run();

}
run_projectpress();




