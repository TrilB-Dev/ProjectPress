<?php
/**
 * Project manager service.
 *
 * @package ProjectPress\Includes\Project
 */

namespace ProjectPress\Includes\Project;

use ProjectPress\Includes\Project\Core\Project;
use ProjectPress\Includes\Project\Core\Workspace;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ProjectManager {
	private static ?self $instance = null;

	private array $projects = array();
	private array $workspaces = array();
	private int $next_project_number = 1;
	private int $next_workspace_number = 1;

	private function __construct() {}

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function create_workspace( string $name, string $description = '', ?string $slug = null ): Workspace {
		$id = 'workspace-' . $this->next_workspace_number;
		$this->next_workspace_number++;

		$workspace = new Workspace( $id, $name, $slug ?: '', $description );
		$this->workspaces[ $id ] = $workspace;

		return $workspace;
	}

	public function create_project( string $title, array $attributes = array() ): Project {
		$id = 'project-' . $this->next_project_number;
		$this->next_project_number++;

		$workspace_id = $attributes['workspace_id'] ?? '';
		$project = new Project(
			$id,
			$title,
			$attributes['description'] ?? '',
			$attributes['status'] ?? Project::STATUS_DRAFT,
			$workspace_id,
			$attributes['metadata'] ?? array()
		);

		$this->projects[ $id ] = $project;

		if ( '' !== $workspace_id && isset( $this->workspaces[ $workspace_id ] ) ) {
			$this->workspaces[ $workspace_id ]->add_project_id( $id );
		}

		return $project;
	}

	public function register_project( Project $project ): self {
		$this->projects[ $project->get_id() ] = $project;

		if ( '' !== $project->get_workspace_id() && isset( $this->workspaces[ $project->get_workspace_id() ] ) ) {
			$this->workspaces[ $project->get_workspace_id() ]->add_project_id( $project->get_id() );
		}

		return $this;
	}

	public function register_workspace( Workspace $workspace ): self {
		$this->workspaces[ $workspace->get_id() ] = $workspace;
		return $this;
	}

	public function get_project( string $id ): ?Project {
		return $this->projects[ $id ] ?? null;
	}

	public function get_workspace( string $id ): ?Workspace {
		return $this->workspaces[ $id ] ?? null;
	}

	public function get_projects(): array {
		return $this->projects;
	}

	public function get_workspaces(): array {
		return $this->workspaces;
	}

	public function get_projects_by_workspace( string $workspace_id ): array {
		return array_values(
			array_filter(
				$this->projects,
				static fn( Project $project ): bool => $project->get_workspace_id() === $workspace_id
			)
		);
	}

	public function get_projects_by_status( string $status ): array {
		return array_values(
			array_filter(
				$this->projects,
				static fn( Project $project ): bool => $project->get_status() === $status
			)
		);
	}

	public function find_project_by_title( string $title ): ?Project {
		foreach ( $this->projects as $project ) {
			if ( $project->get_title() === $title ) {
				return $project;
			}
		}

		return null;
	}

	public function delete_project( string $id ): bool {
		if ( ! isset( $this->projects[ $id ] ) ) {
			return false;
		}

		$project = $this->projects[ $id ];
		$workspace_id = $project->get_workspace_id();
		if ( '' !== $workspace_id && isset( $this->workspaces[ $workspace_id ] ) ) {
			$this->workspaces[ $workspace_id ]->remove_project_id( $id );
		}

		unset( $this->projects[ $id ] );
		return true;
	}

	public function delete_workspace( string $id ): bool {
		if ( ! isset( $this->workspaces[ $id ] ) ) {
			return false;
		}

		$workspace = $this->workspaces[ $id ];
		foreach ( $workspace->get_project_ids() as $project_id ) {
			if ( isset( $this->projects[ $project_id ] ) ) {
				$this->projects[ $project_id ]->set_workspace_id( '' );
			}
		}

		unset( $this->workspaces[ $id ] );
		return true;
	}
}
