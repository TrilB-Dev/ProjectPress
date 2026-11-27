<?php
/**
 * CronJobs class for registering plugin cron jobs.
 *
 * @package ProjectPress
 * @subpackage Includes\Pages
 * @since 1.0.0
 */
namespace ProjectPress\Includes\Pages;

use ProjectPress\Includes\Core\CronJobs as CronJobCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CronJobs {
	/**
	 * Register all page-layer cron jobs for the plugin.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register(): void {
		self::projectpress_cleanup();
	}

	/**
	 * Register the built-in ProjectPress cleanup cron job.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function projectpress_cleanup(): bool {
		return CronJobCore::create(
			'core',
			'projectpress_cleanup',
			'daily',
			array(),
			array( self::class, 'cleanup' ),
			__( 'Daily ProjectPress cleanup task.', 'projectpress' )
		);
	}

	/**
	 * Example built-in cron callback.
	 *
	 * @return void
	 */
	public static function cleanup(): void {
		do_action( 'projectpress_daily_cleanup' );
	}
}

