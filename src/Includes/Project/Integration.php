<?php
/**
 * ProjectPress domain integration entry point.
 *
 * @package ProjectPress\Includes\Project
 */

namespace ProjectPress\Includes\Project;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Integration {
	private ProjectManager $project_manager;

	public function __construct( ?ProjectManager $project_manager = null ) {
		$this->project_manager = $project_manager ?: ProjectManager::get_instance();
	}

	public function register(): self {
		return $this;
	}

	public function get_project_manager(): ProjectManager {
		return $this->project_manager;
	}
}
