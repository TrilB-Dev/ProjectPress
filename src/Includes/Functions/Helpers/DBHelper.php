<?php
/**
 * Helper facade for ProjectPress custom database tables.
 *
 * @package ProjectPress\Includes\Functions\Helpers
 */
namespace ProjectPress\Includes\Functions\Helpers;

use ProjectPress\Includes\Core\WP\Database;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class DBHelper {
	public static function register_core_table( string $table, callable $schema ): bool { return Database::register_core_table( $table, $schema ); }
	public static function register_plugin_table( string $table, callable $schema ): bool { return Database::register_plugin_table( $table, $schema ); }
	public static function get_core_tables(): array { return Database::get_core_tables(); }
	public static function get_plugin_tables(): array { return Database::get_plugin_tables(); }
	public static function table_name( string $table ): string { return Database::table_name( $table ); }
	public static function table( string $table ): string { return self::table_name( $table ); }
	public static function table_exists( string $table ): bool { return Database::table_exists( $table ); }
	public static function create_table( string $table ): bool { return Database::create_table( $table ); }
	public static function install(): bool { return Database::install(); }
	public static function update_schema(): bool { return Database::update(); }
	public static function synchronise(): bool { return Database::synchronise(); }
	public static function drop_table_contents( string $table ): bool { return Database::drop_table_contents( $table ); }
	public static function truncate_table( string $table ): bool { return Database::truncate_table( $table ); }
	public static function delete_table( string $table ): bool { return Database::delete_table( $table ); }
	public static function drop_table( string $table ): bool { return Database::drop_table( $table ); }

	public static function get_row( string $table, array $where = array(), string $columns = '*', string $order_by = '' ) {
		$rows = self::get_results( $table, $where, $columns, $order_by, 1 );
		return $rows[0] ?? null;
	}

	public static function get_results( string $table, array $where = array(), string $columns = '*', string $order_by = '', int $limit = 0, int $offset = 0 ): array {
		global $wpdb;
		$table_name = self::table_name( $table );
		if ( '' === $table_name ) { return array(); }
		$query = 'SELECT ' . self::sanitize_columns( $columns ) . " FROM `{$table_name}`";
		$args = array();
		$where_sql = self::where_sql( $where, $args );
		if ( '' !== $where_sql ) { $query .= ' WHERE ' . $where_sql; }
		$order_sql = self::order_sql( $order_by );
		if ( '' !== $order_sql ) { $query .= ' ORDER BY ' . $order_sql; }
		if ( $limit > 0 ) { $query .= ' LIMIT %d OFFSET %d'; $args[] = $limit; $args[] = max( 0, $offset ); }
		if ( $args ) { $query = $wpdb->prepare( $query, $args ); }
		$rows = $wpdb->get_results( $query, ARRAY_A );
		return is_array( $rows ) ? $rows : array();
	}

	public static function get_var( string $table, string $column, array $where = array(), $default = null ) {
		$row = self::get_row( $table, $where, $column );
		if ( ! is_array( $row ) || ! array_key_exists( $column, $row ) ) { return $default; }
		return $row[ $column ];
	}
	public static function get_all( string $table, array $where = array(), string $columns = '*', string $order_by = '' ): array { return self::get_results( $table, $where, $columns, $order_by ); }
	public static function count( string $table, array $where = array() ): int { return (int) self::get_var( $table, 'COUNT(*) AS total', $where, 0 ); }
	public static function exists( string $table, array $where = array() ): bool { return self::count( $table, $where ) > 0; }

	public static function insert( string $table, array $data, array $formats = array() ) {
		global $wpdb;
		$name = self::table_name( $table );
		if ( '' === $name || empty( $data ) ) { return false; }
		$result = $wpdb->insert( $name, $data, $formats ?: null );
		return false === $result ? false : (int) $wpdb->insert_id;
	}
	public static function update( string $table, array $data, array $where, array $formats = array(), array $where_formats = array() ): bool {
		global $wpdb;
		$name = self::table_name( $table );
		return '' !== $name && ! empty( $data ) && ! empty( $where ) && false !== $wpdb->update( $name, $data, $where, $formats ?: null, $where_formats ?: null );
	}
	public static function delete( string $table, array $where, array $where_formats = array() ): bool {
		global $wpdb;
		$name = self::table_name( $table );
		return '' !== $name && ! empty( $where ) && false !== $wpdb->delete( $name, $where, $where_formats ?: null );
	}
	public static function insert_or_update( string $table, array $data, array $where = array() ) {
		if ( empty( $where ) ) { return self::insert( $table, $data ); }
		if ( self::exists( $table, $where ) ) { return self::update( $table, $data, $where ) ? ( self::get_var( $table, 'id', $where, true ) ) : false; }
		return self::insert( $table, array_merge( $data, $where ) );
	}
	public static function get_by_id( string $table, int $id, string $id_column = 'id' ) { return self::get_row( $table, array( $id_column => $id ) ); }

	private static function where_sql( array $where, array &$args ): string {
		$parts = array();
		foreach ( $where as $column => $value ) {
			$column = self::identifier( (string) $column );
			if ( '' === $column ) { continue; }
			if ( is_array( $value ) && $value ) { $parts[] = "`{$column}` IN (" . implode( ',', array_fill( 0, count( $value ), '%s' ) ) . ')'; array_push( $args, ...array_values( $value ) ); }
			else { $parts[] = "`{$column}` = %s"; $args[] = $value; }
		}
		return implode( ' AND ', $parts );
	}
	private static function sanitize_columns( string $columns ): string {
		if ( '*' === trim( $columns ) ) { return '*'; }
		$clean = array();
		foreach ( explode( ',', $columns ) as $column ) { $column = trim( $column ); if ( preg_match( '/^COUNT\(\*\) AS ([A-Za-z0-9_]+)$/i', $column, $m ) ) { $clean[] = 'COUNT(*) AS `' . $m[1] . '`'; continue; } $id = self::identifier( $column ); if ( '' !== $id ) { $clean[] = "`{$id}`"; } }
		return $clean ? implode( ', ', $clean ) : '*';
	}
	private static function order_sql( string $order_by ): string {
		$parts = array();
		foreach ( explode( ',', $order_by ) as $part ) { if ( preg_match( '/^\s*([A-Za-z0-9_]+)(?:\s+(ASC|DESC))?\s*$/i', $part, $m ) ) { $parts[] = '`' . $m[1] . '`' . ( isset( $m[2] ) ? ' ' . strtoupper( $m[2] ) : '' ); } }
		return implode( ', ', $parts );
	}
	private static function identifier( string $value ): string { return preg_match( '/^[A-Za-z0-9_]+$/', $value ) ? $value : ''; }
}
