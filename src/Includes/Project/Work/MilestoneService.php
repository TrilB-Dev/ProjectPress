<?php
/**
 * Milestone workflow service.
 *
 * @package ProjectPress
 */
namespace ProjectPress\Includes\Project\Work;

use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\ProjectRepository;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MilestoneService {
	private MilestoneRepository $milestones;
	private ProjectRepository $projects;
	private ProjectPermissions $permissions;

	public function __construct( MilestoneRepository $milestones, ProjectRepository $projects, ProjectPermissions $permissions ) {
		$this->milestones  = $milestones;
		$this->projects    = $projects;
		$this->permissions = $permissions;
	}

	public static function statuses(): array {
		return array( 'planned', 'active', 'completed', 'cancelled' );
	}

	public function create( int $project_id, array $input ) {
		$project = $this->projects->find( $project_id );
		if ( ! $project || ! $this->permissions->can_edit( $project ) ) {
			return new WP_Error( 'projectpress_milestone_forbidden', __( 'You do not have permission to add milestones to this project.', 'projectpress' ) );
		}

		$data = $this->validate( $input );
		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$now                = current_time( 'mysql', true );
		$data['project_id'] = $project_id;
		$data['created_by'] = get_current_user_id();
		$data['created_at'] = $now;
		$data['updated_by'] = get_current_user_id();
		$data['updated_at'] = $now;
		$id                 = $this->milestones->create( $data );

		return false === $id ? new WP_Error( 'projectpress_milestone_create_failed', __( 'The milestone could not be created.', 'projectpress' ) ) : $this->milestones->find( (int) $id );
	}

	public function delete( Milestone $milestone ) {
		$project = $this->projects->find( $milestone->get_project_id() );
		if ( ! $project || ! $this->permissions->can_edit( $project ) ) {
			return new WP_Error( 'projectpress_milestone_forbidden', __( 'You do not have permission to delete milestones in this project.', 'projectpress' ) );
		}
		return $this->milestones->delete( $milestone->get_id() ) ? true : new WP_Error( 'projectpress_milestone_delete_failed', __( 'The milestone could not be deleted.', 'projectpress' ) );
	}

	private function validate( array $input ) {
		$title = sanitize_text_field( $input['title'] ?? '' );
		if ( '' === $title ) {
			return new WP_Error( 'projectpress_milestone_title_required', __( 'A milestone title is required.', 'projectpress' ) );
		}

		$status = sanitize_key( $input['status'] ?? 'planned' );
		if ( ! in_array( $status, self::statuses(), true ) ) {
			return new WP_Error( 'projectpress_milestone_status_invalid', __( 'The milestone status is invalid.', 'projectpress' ) );
		}

		$target = sanitize_text_field( $input['target_date'] ?? '' );
		if ( '' !== $target && false === strtotime( $target ) ) {
			return new WP_Error( 'projectpress_milestone_date_invalid', __( 'The milestone target date is invalid.', 'projectpress' ) );
		}

		return array(
			'title'       => $title,
			'description' => sanitize_textarea_field( $input['description'] ?? '' ),
			'status'      => $status,
			'target_date' => '' === $target ? null : gmdate( 'Y-m-d H:i:s', strtotime( $target ) ),
		);
	}
}
