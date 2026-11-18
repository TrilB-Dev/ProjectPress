<?php
namespace ProjectPress\Includes\Project\Core;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class ProjectService {
	private ProjectRepository $repository;
	private ProjectValidator $validator;
	private ProjectPermissions $permissions;
	public function __construct( ProjectRepository $repository, ProjectValidator $validator, ProjectPermissions $permissions ) { $this->repository = $repository; $this->validator = $validator; $this->permissions = $permissions; }

	public function create( array $input ) {
		if ( ! $this->permissions->can_create() ) { return new WP_Error( 'projectpress_create_forbidden', __( 'You do not have permission to create projects.', 'projectpress' ) ); }
		$data = $this->validator->validate( $input );
		if ( is_wp_error( $data ) ) { return $data; }
		$data['slug'] = $data['slug'] ?? sanitize_title( $data['name'] );
		$data['key'] = $data['key'] ?? strtoupper( substr( sanitize_key( $data['name'] ), 0, 24 ) . '-' . wp_unique_id() );
		$data['owner_id'] = $data['owner_id'] ?? get_current_user_id();
		$data['created_by'] = get_current_user_id(); $data['updated_by'] = get_current_user_id();
		$project = new Project( $data );
		$id = $this->repository->insert( $project );
		if ( false === $id ) { return new WP_Error( 'projectpress_project_create_failed', __( 'The project could not be created.', 'projectpress' ) ); }
		do_action( 'projectpress_project_created', $id, $project );
		return $this->repository->find( $id );
	}

	public function update( Project $project, array $input ) {
		if ( ! $this->permissions->can_edit( $project ) ) { return new WP_Error( 'projectpress_edit_forbidden', __( 'You do not have permission to edit this project.', 'projectpress' ) ); }
		$data = $this->validator->validate( $input, true );
		if ( is_wp_error( $data ) ) { return $data; }
		$merged = array_merge( $project->to_array(), $data, array( 'updated_by' => get_current_user_id() ) );
		$updated = new Project( $merged );
		if ( ! $this->repository->update( $updated ) ) { return new WP_Error( 'projectpress_project_update_failed', __( 'The project could not be updated.', 'projectpress' ) ); }
		do_action( 'projectpress_project_updated', $updated );
		return $this->repository->find( $updated->get_id() );
	}

	public function delete( Project $project ) {
		if ( ! $this->permissions->can_delete( $project ) ) { return new WP_Error( 'projectpress_delete_forbidden', __( 'You do not have permission to delete this project.', 'projectpress' ) ); }
		if ( ! $this->repository->delete( $project->get_id() ) ) { return new WP_Error( 'projectpress_project_delete_failed', __( 'The project could not be deleted.', 'projectpress' ) ); }
		do_action( 'projectpress_project_deleted', $project->get_id(), $project );
		return true;
	}
}
