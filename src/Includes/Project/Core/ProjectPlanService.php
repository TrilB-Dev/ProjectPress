<?php
/**
 * Project plan read service.
 *
 * @package ProjectPress
 */
namespace ProjectPress\Includes\Project\Core;

use ProjectPress\Includes\Project\Work\MilestoneRepository;
use ProjectPress\Includes\Project\Work\TaskRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ProjectPlanService {
	private ProjectRepository $projects;
	private ProjectPermissions $permissions;
	private MilestoneRepository $milestones;
	private TaskRepository $tasks;

	public function __construct( ProjectRepository $projects, ProjectPermissions $permissions, MilestoneRepository $milestones, TaskRepository $tasks ) {
		$this->projects    = $projects;
		$this->permissions = $permissions;
		$this->milestones  = $milestones;
		$this->tasks       = $tasks;
	}

	public function for_project( int $project_id ): ?ProjectPlan {
		$project = $this->projects->find( absint( $project_id ) );
		if ( ! $project || ! $this->permissions->can_view( $project ) ) {
			return null;
		}

		return new ProjectPlan(
			$project->get_id(),
			$this->milestones->for_project( $project->get_id() ),
			$this->tasks->for_project( $project->get_id() )
		);
	}
}
