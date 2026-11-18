<?php
namespace ProjectPress\Includes\Project\Core;

use ProjectPress\Includes\Core\WP\Database;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class ProjectRepository {
	private ProjectHydrator $hydrator;
	public function __construct( ?ProjectHydrator $hydrator = null ) { $this->hydrator = $hydrator ?? new ProjectHydrator(); }

	public function find( int $id ): ?Project { return $this->find_by( 'id', $id ); }
	public function find_by_key( string $key ): ?Project { return $this->find_by( 'project_key', $key ); }
	public function find_by_slug( string $slug ): ?Project { return $this->find_by( 'project_slug', $slug ); }

	private function find_by( string $column, $value ): ?Project {
		global $wpdb;
		$table = Database::table_name( 'projects' );
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE {$column} = %s LIMIT 1", $value ), ARRAY_A );
		return is_array( $row ) ? $this->hydrator->hydrate( $row ) : null;
	}

	public function query( ProjectQuery $query ): array {
		global $wpdb;
		$table = Database::table_name( 'projects' );
		$args = $query->to_array();
		$where = array( '1=1' );
		$values = array();
		foreach ( array( 'status', 'priority', 'visibility' ) as $field ) {
			if ( '' !== $args[ $field ] ) { $where[] = "{$field} = %s"; $values[] = $args[ $field ]; }
		}
		foreach ( array( 'owner_id', 'type_id', 'parent_id' ) as $field ) {
			if ( $args[ $field ] > 0 ) { $where[] = "{$field} = %d"; $values[] = $args[ $field ]; }
		}
		if ( '' !== $args['search'] ) { $like = '%' . $wpdb->esc_like( $args['search'] ) . '%'; $where[] = '(project_name LIKE %s OR project_description LIKE %s OR project_key LIKE %s)'; array_push( $values, $like, $like, $like ); }
		$allowed_order = array( 'id', 'project_name', 'status', 'priority', 'start_date', 'target_end_date', 'created_at', 'updated_at' );
		$order_by = in_array( $args['order_by'], $allowed_order, true ) ? $args['order_by'] : 'updated_at';
		$offset = ( $args['page'] - 1 ) * $args['per_page'];
		$sql = "SELECT * FROM {$table} WHERE " . implode( ' AND ', $where ) . " ORDER BY {$order_by} {$args['order']} LIMIT %d OFFSET %d";
		$values[] = $args['per_page']; $values[] = $offset;
		$rows = $wpdb->get_results( $wpdb->prepare( $sql, $values ), ARRAY_A );
		return array_map( array( $this->hydrator, 'hydrate' ), is_array( $rows ) ? $rows : array() );
	}

	public function insert( Project $project ) {
		global $wpdb;
		$data = array_filter( $this->hydrator->extract( $project ), static fn( $value ) => null !== $value );
		$data['created_at'] = current_time( 'mysql', true ); $data['updated_at'] = $data['created_at'];
		$result = $wpdb->insert( Database::table_name( 'projects' ), $data );
		return false === $result ? false : (int) $wpdb->insert_id;
	}

	public function update( Project $project ): bool {
		if ( $project->get_id() < 1 ) { return false; }
		global $wpdb;
		$data = $this->hydrator->extract( $project ); unset( $data['created_by'] );
		$data['updated_at'] = current_time( 'mysql', true );
		return false !== $wpdb->update( Database::table_name( 'projects' ), $data, array( 'id' => $project->get_id() ) );
	}

	public function delete( int $id ): bool {
		global $wpdb;
		return false !== $wpdb->delete( Database::table_name( 'projects' ), array( 'id' => absint( $id ) ), array( '%d' ) );
	}
}
