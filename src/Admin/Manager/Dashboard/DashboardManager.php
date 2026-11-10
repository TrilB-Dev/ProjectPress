<?php
/**
 * DashboardManager class for the ProjectPress plugin.
 *
 * @package ProjectPress
 */
namespace ProjectPress\Admin\Manager\Dashboard;

use ProjectPress\Admin\Manager\Manager;
use ProjectPress\Assets\Assets;
use ProjectPress\Includes\UserManagement\Groups\Groups;
use ProjectPress\Includes\UserManagement\Roles\Roles;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DashboardManager extends Manager {
	/**
	 * The slug for the dashboard page.
	 *
	 * @var string
	 */
	protected string $page;

	/**
	 * Constructor for the DashboardManager class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->page = 'projectpress';
	}

	/**
	 * Render the dashboard page via the main dashboard UI class.
	 *
	 * @since 1.0.0
	 */
	public function render(): void {
		( new DashboardMain() )->render_page_content();
	}

	/**
	 * Register the assets required for the dashboard page.
	 *
	 * @param Assets $assets The assets manager instance.
	 * @since 1.0.0
	 */
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, array( 'projectpress' ), 'dashboard' );
	}
}





