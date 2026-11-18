<?php
/**
 * ProjectPress custom-table registry and installer.
 *
 * @package ProjectPress\Includes\Core\WP
 */
namespace ProjectPress\Includes\Core\WP;

use ProjectPress\Includes\Core\Schema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Database {
	private static array $registered_plugin_tables = array();
	private static array $registered_core_tables = array();

	private static function normalize_table_key( string $table ): string {
		return sanitize_key( $table );
	}

	public static function register_core_table( string $table, callable $schema ): bool {
		$table = self::normalize_table_key( $table );
		if ( '' === $table ) { return false; }
		self::$registered_core_tables[ $table ] = $schema;
		return true;
	}

	public static function register_plugin_table( string $table, callable $schema ): bool {
		$table = self::normalize_table_key( $table );
		if ( '' === $table ) { return false; }
		self::$registered_plugin_tables[ $table ] = $schema;
		return true;
	}

	public static function get_core_tables(): array { return self::$registered_core_tables; }
	public static function get_plugin_tables(): array { return self::$registered_plugin_tables; }

	public static function table_name( string $table ): string {
		global $wpdb;
		$table = self::normalize_table_key( $table );
		return '' === $table ? '' : $wpdb->prefix . 'projectpress_' . $table;
	}

	public static function table_exists( string $table ): bool {
		global $wpdb;
		$name = self::table_name( $table );
		return '' !== $name && $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $name ) ) ) === $name;
	}

	public static function create_table( string $table ): bool {
		global $wpdb;
		$table = self::normalize_table_key( $table );
		$schema = self::$registered_core_tables[ $table ] ?? self::$registered_plugin_tables[ $table ] ?? null;
		if ( '' === $table || ! is_callable( $schema ) ) { return false; }
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$statement = call_user_func( $schema, self::table_name( $table ), $wpdb->get_charset_collate() );
		if ( ! is_string( $statement ) || '' === trim( $statement ) ) { return false; }
		$result = dbDelta( $statement );
		return self::table_exists( $table ) && is_array( $result );
	}

	public static function install(): bool { return self::synchronise(); }
	public static function update(): bool { return self::synchronise(); }

	public static function synchronise(): bool {
		Schema::register_tables();
		$success = true;
		foreach ( array_keys( array_merge( self::$registered_core_tables, self::$registered_plugin_tables ) ) as $table ) {
			if ( ! self::create_table( $table ) ) { $success = false; }
		}
		if ( $success && function_exists( 'update_option' ) ) {
			update_option( 'projectpress_db_version', defined( 'PROJECTPRESS_VERSION' ) ? PROJECTPRESS_VERSION : '1.0.0' );
		}
		return $success;
	}

	public static function drop_table_contents( string $table ): bool {
		global $wpdb;
		$name = self::table_name( $table );
		return '' !== $name && self::table_exists( $table ) && false !== $wpdb->query( "TRUNCATE TABLE `{$name}`" );
	}
	public static function truncate_table( string $table ): bool { return self::drop_table_contents( $table ); }
	public static function delete_table( string $table ): bool {
		global $wpdb;
		$name = self::table_name( $table );
		return '' !== $name && self::table_exists( $table ) && false !== $wpdb->query( "DROP TABLE IF EXISTS `{$name}`" );
	}
	public static function drop_table( string $table ): bool { return self::delete_table( $table ); }
}
