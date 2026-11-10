<?php
/**
 * Define the activation functionality for the plugin.
 *
 * Handles the registration and execution of activation callbacks.
 *
 * @since 1.0.0
 *
 * @package    ProjectPress
 * @subpackage ProjectPress/Includes/Core/WP
 */
namespace ProjectPress\Includes\Core\WP;

use ProjectPress\Includes\Core\Capabilities;
use ProjectPress\Includes\Core\Roles;
use ProjectPress\Includes\ModManagement\GroupsManager;
use ProjectPress\Includes\Plugins\Plugins;
use ProjectPress\Includes\Settings\SettingsManager;
use ProjectPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Activator {
	/**
	 * Registered activation callbacks.
	 *
	 * @var array<int, callable>
	 */
	private static array $callbacks = array();

	/**
	 * Register extension activation callbacks.
	 *
	 * @param callable $callback Callback invoked during activation.
	 * @return void
	 */
	public static function register( callable $callback ): void {
		self::$callbacks[] = $callback;
	}

	/**
	 * Run ProjectPress and extension activation tasks.
	 *
	 * @param array<int, callable>|null $callbacks Optional callbacks for this run.
	 * @return void
	 */
	public static function activate( ?array $callbacks = null ): void {
		Settings::register_group(
			'setup',
			array(
				'first_install_complete'    => false,
				'onboarding_steps_complete' => 0,
			)
		);

		Plugins::get_instance()->init();
		Database::install();
		Database::update();
		GroupsManager::create_default_group();
		Roles::install();
		Capabilities::install();
		SettingsManager::install();

		foreach ( $callbacks ?? self::$callbacks as $callback ) {
			call_user_func( $callback );
		}

		flush_rewrite_rules();
	}
}
