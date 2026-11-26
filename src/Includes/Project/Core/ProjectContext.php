<?php
/**
 * Resolves project context and access.
 *
 * @package ProjectPress\Includes\Project\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Core;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolves project identifiers and permission access.
 *
 * @since 1.0.0
 */
final class ProjectContext {
	/**
	 * Project repository.
	 *
	 * @var ProjectRepository
	 * @since 1.0.0
	 */
	private ProjectRepository $repository;

	/**
	 * Project permissions.
	 *
	 * @var ProjectPermissions
	 * @since 1.0.0
	 */
	private ProjectPermissions $permissions;

	/**
	 * Initialise the project context resolver.
	 *
	 * @param ProjectRepository  $repository Project repository.
	 * @param ProjectPermissions $permissions Project permissions.
	 * @since 1.0.0
	 */
	public function __construct( ProjectRepository $repository, ProjectPermissions $permissions ) {
		$this->repository = $repository;
		$this->permissions = $permissions;
	}

	/**
	 * Resolve an identifier and verify view access.
	 *
	 * @param int|string $identifier Project ID, key, or slug.
	 * @return Project|WP_Error
	 * @since 1.0.0
	 */
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
