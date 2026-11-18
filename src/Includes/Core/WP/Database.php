<?php
/** ProjectPress custom-table registry and installer. */
namespace ProjectPress\Includes\Core\WP;

use ProjectPress\Includes\Core\Schema;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Database {
	private static array $registered_plugin_tables = array();
	private static array $registered_core_tables = array();

	private static function normalize_table_key( string $table ): string { return sanitize_key( $table ); }
	public static function register_core_table( string $table, callable $schema ): bool { $table = self::normalize_table_key( $table ); if ( '' === $table ) { return false; } self::$registered_core_tables[ $table ] = $schema; return true; }
	public static function register_plugin_table( string $table, callable $schema ): bool { $table = self::normalize_table_key( $table ); if ( '' === $table ) { return false; } self::$registered_plugin_tables[ $table ] = $schema; return true; }
	public static function get_core_tables(): array { return self::$registered_core_tables; }
	public static function get_plugin_tables(): array { return self::$registered_plugin_tables; }
	public static function table_name( string $table ): string { global $wpdb; return $wpdb->prefix . 'projectpress_' . self::normalize_table_key( $table ); }
	public static function table_exists( string $table ): bool { global $wpdb; $name = self::table_name( $table ); return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $name ) ) === $name; }

	public static function create_table( string $table ): bool {
		global $wpdb;
		$table = self::normalize_table_key( $table );
		$schema = self::$registered_core_tables[ $table ] ?? self::$registered_plugin_tables[ $table ] ?? null;
		if ( '' === $table || ! is_callable( $schema ) ) { return false; }
		if ( file_exists( ABSPATH . 'wp-admin/includes/upgrade.php' ) ) { require_once ABSPATH . 'wp-admin/includes/upgrade.php'; }
		$statement = call_user_func( $schema, self::table_name( $table ), $wpdb->get_charset_collate() );
		if ( ! is_string( $statement ) || '' === trim( $statement ) ) { return false; }
		dbDelta( $statement );
		return self::table_exists( $table );
	}

	public static function install(): void { self::synchronise(); }
	public static function update(): void { self::synchronise(); }
	private static function synchronise(): void {
		Schema::register_tables();
		foreach ( array_keys( array_merge( self::$registered_core_tables, self::$registered_plugin_tables ) ) as $table ) { self::create_table( $table ); }
		if ( function_exists( 'update_option' ) ) { update_option( 'projectpress_db_version', defined( 'PROJECTPRESS_VERSION' ) ? PROJECTPRESS_VERSION : '1.0.0' ); }
	}

	public static function drop_table_contents( string $table ): bool { global $wpdb; return self::table_exists( $table ) && false !== $wpdb->query( 'TRUNCATE TABLE ' . self::table_name( $table ) ); }
	public static function truncate_table( string $table ): bool { return self::drop_table_contents( $table ); }
	public static function delete_table( string $table ): bool { global $wpdb; return self::table_exists( $table ) && false !== $wpdb->query( 'DROP TABLE IF EXISTS ' . self::table_name( $table ) ); }
	public static function drop_table( string $table ): bool { return self::delete_table( $table ); }
}
