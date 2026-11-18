<?php
namespace ProjectPress\Includes\Project\Core;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class ProjectContext {
	private ProjectRepository $repository;
	private ProjectPermissions $permissions;
	public function __construct( ProjectRepository $repository, ProjectPermissions $permissions ) { $this->repository = $repository; $this->permissions = $permissions; }

	public function resolve( $identifier ) {
		$project = null;
		if ( is_numeric( $identifier ) ) { $project = $this->repository->find( absint( $identifier ) ); }
		elseif ( is_string( $identifier ) && '' !== trim( $identifier ) ) {
			$project = $this->repository->find_by_key( sanitize_key( $identifier ) );
			$project = $project ?? $this->repository->find_by_slug( sanitize_title( $identifier ) );
		}
		if ( ! $project ) { return new WP_Error( 'projectpress_project_not_found', __( 'The requested project could not be found.', 'projectpress' ), array( 'status' => 404 ) ); }
		if ( ! $this->permissions->can_view( $project ) ) { return new WP_Error( 'projectpress_project_forbidden', __( 'You do not have permission to access this project.', 'projectpress' ), array( 'status' => 403 ) ); }
		return $project;
	}
}
