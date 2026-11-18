<?php
namespace ProjectPress\Includes\Project\Core;
use ProjectPress\Includes\Core\WP\Database;
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class TeamRepository {
	public function all(): array { global $wpdb; return (array) $wpdb->get_results( 'SELECT * FROM ' . Database::table_name( 'teams' ) . ' ORDER BY team_name ASC', ARRAY_A ); }
	public function create( array $data ) { global $wpdb; $row = array( 'team_name' => sanitize_text_field( $data['name'] ?? '' ), 'team_slug' => sanitize_title( $data['slug'] ?? $data['name'] ?? '' ), 'team_description' => sanitize_textarea_field( $data['description'] ?? '' ), 'owner_id' => absint( $data['owner_id'] ?? get_current_user_id() ), 'status' => sanitize_key( $data['status'] ?? 'active' ), 'created_by' => get_current_user_id(), 'created_at' => current_time( 'mysql', true ), 'updated_by' => get_current_user_id(), 'updated_at' => current_time( 'mysql', true ) ); if ( '' === $row['team_name'] || '' === $row['team_slug'] ) { return false; } return false === $wpdb->insert( Database::table_name( 'teams' ), $row ) ? false : (int) $wpdb->insert_id; }
	public function add_member( int $team_id, int $user_id, string $role = 'member' ): bool { global $wpdb; return false !== $wpdb->replace( Database::table_name( 'team_members' ), array( 'team_id' => $team_id, 'user_id' => $user_id, 'member_role' => sanitize_key( $role ), 'member_status' => 'active', 'added_by' => get_current_user_id(), 'joined_at' => current_time( 'mysql', true ), 'created_at' => current_time( 'mysql', true ), 'updated_at' => current_time( 'mysql', true ) ) ); }
	public function assign_project( int $team_id, int $project_id, string $role = 'contributor' ): bool { global $wpdb; return false !== $wpdb->replace( Database::table_name( 'project_teams' ), array( 'team_id' => $team_id, 'project_id' => $project_id, 'team_role' => sanitize_key( $role ), 'assigned_by' => get_current_user_id(), 'assigned_at' => current_time( 'mysql', true ) ) ); }
}
