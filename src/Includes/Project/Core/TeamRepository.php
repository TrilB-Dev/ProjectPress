<?php
/**
 * Persistence for ProjectPress teams and project/team relationships.
 *
 * @package ProjectPress\Includes\Project\Core
 */
namespace ProjectPress\Includes\Project\Core;

use ProjectPress\Includes\Functions\Helpers\DBHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class TeamRepository {
	public function all(): array {
		return DBHelper::get_all( 'teams', array(), '*', 'team_name ASC' );
	}

	public function find( int $team_id ): ?array {
		return DBHelper::get_by_id( 'teams', absint( $team_id ) );
	}

	public function create( array $data ) {
		return DBHelper::insert(
			'teams',
			$data,
			array( '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%d', '%s' )
		);
	}

	public function update( int $team_id, array $data ): bool {
		return DBHelper::update( 'teams', $data, array( 'id' => absint( $team_id ) ) );
	}

	public function members( int $team_id ): array {
		return DBHelper::get_all( 'team_members', array( 'team_id' => absint( $team_id ) ), '*', 'created_at ASC' );
	}

	public function add_member( int $team_id, int $user_id, string $role, string $status = 'active' ): bool {
		$existing = DBHelper::get_row( 'team_members', array( 'team_id' => $team_id, 'user_id' => $user_id ) );
		$data = array(
			'member_role' => $role,
			'member_status' => $status,
			'updated_at' => current_time( 'mysql', true ),
		);
		if ( $existing ) {
			return DBHelper::update( 'team_members', $data, array( 'id' => (int) $existing['id'] ) );
		}
		$data = array_merge( array(
			'team_id' => $team_id,
			'user_id' => $user_id,
			'added_by' => get_current_user_id(),
			'joined_at' => current_time( 'mysql', true ),
			'created_at' => current_time( 'mysql', true ),
		), $data );
		return false !== DBHelper::insert( 'team_members', $data );
	}

	public function remove_member( int $team_id, int $user_id ): bool {
		return DBHelper::delete( 'team_members', array( 'team_id' => $team_id, 'user_id' => $user_id ) );
	}

	public function projects( int $team_id ): array {
		return DBHelper::get_all( 'project_teams', array( 'team_id' => absint( $team_id ) ), '*', 'assigned_at ASC' );
	}

	public function assign_project( int $team_id, int $project_id, string $role ): bool {
		$existing = DBHelper::get_row( 'project_teams', array( 'team_id' => $team_id, 'project_id' => $project_id ) );
		if ( $existing ) {
			return DBHelper::update( 'project_teams', array( 'team_role' => $role ), array( 'id' => (int) $existing['id'] ) );
		}
		return false !== DBHelper::insert( 'project_teams', array(
			'team_id' => $team_id,
			'project_id' => $project_id,
			'team_role' => $role,
			'assigned_by' => get_current_user_id(),
			'assigned_at' => current_time( 'mysql', true ),
		) );
	}

	public function remove_project( int $team_id, int $project_id ): bool {
		return DBHelper::delete( 'project_teams', array( 'team_id' => $team_id, 'project_id' => $project_id ) );
	}
}
