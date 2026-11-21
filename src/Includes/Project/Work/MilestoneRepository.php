<?php
/**
 * Milestone persistence repository.
 *
 * @package ProjectPress
 */
namespace ProjectPress\Includes\Project\Work;

use ProjectPress\Includes\Functions\Helpers\DBHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class MilestoneRepository {
	public function for_project( int $project_id ): array {
		return array_map(
			static fn( $row ) => new Milestone( $row ),
			DBHelper::get_all( 'milestones', array( 'project_id' => absint( $project_id ) ), '*', 'sort_order ASC, created_at ASC' )
		);
	}

	public function find( int $id ): ?Milestone {
		$row = DBHelper::get_by_id( 'milestones', absint( $id ) );
		return $row ? new Milestone( $row ) : null;
	}

	public function create( array $data ) {
		return DBHelper::insert( 'milestones', $data );
	}

	public function update( int $id, array $data ): bool {
		return DBHelper::update( 'milestones', $data, array( 'id' => absint( $id ) ) );
	}

	public function delete( int $id ): bool {
		return DBHelper::delete( 'milestones', array( 'id' => absint( $id ) ) );
	}
}
