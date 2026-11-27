<?php
/**
 * Public settings facade for ProjectPress.
 *
 * @package ProjectPress
 * @subpackage Includes\Settings
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Settings;

use ProjectPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides typed access to ProjectPress settings.
 *
 * @since 1.0.0
 */
final class Settings {
	public const GENERAL       = 'general';
	public const PROJECTS      = 'projects';
	public const LAYOUT        = 'layout';
	public const ACCESS        = 'access';
	public const NOTIFICATIONS = 'notifications';
	public const ACTIVITY      = 'activity';
	public const TOOLS         = 'tools';

	/** Get a setting value. @since 1.0.0 */
	public static function get( string $key, $default = null ) {
		return SettingsManager::get( $key, $default );
	}

	/** Get a string setting. @since 1.0.0 */
	public static function get_string( string $key, string $default = '' ): string {
		return SanitizationHelper::text( self::get( $key, $default ), $default );
	}

	/** Get a key setting. @since 1.0.0 */
	public static function get_key( string $key, string $default = '' ): string {
		return SanitizationHelper::key( self::get( $key, $default ), $default );
	}

	/** Get a slug setting. @since 1.0.0 */
	public static function get_slug( string $key, string $default = '' ): string {
		return SanitizationHelper::slug( self::get( $key, $default ), $default );
	}

	/** Get an integer setting. @since 1.0.0 */
	public static function get_int( string $key, int $default = 0 ): int {
		return SanitizationHelper::integer( self::get( $key, $default ), $default );
	}

	/** Get a boolean setting. @since 1.0.0 */
	public static function get_bool( string $key, bool $default = false ): bool {
		$value = self::get( $key, $default );
		if ( is_bool( $value ) ) {
			return $value;
		}
		if ( ! is_scalar( $value ) ) {
			return $default;
		}
		$parsed = filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
		return null === $parsed ? $default : $parsed;
	}

	/** Set a setting. @since 1.0.0 */
	public static function set( string $key, $value ): bool {
		return SettingsManager::set( $key, $value );
	}

	/** Delete a setting. @since 1.0.0 */
	public static function delete( string $key ): bool {
		return SettingsManager::delete( $key );
	}

	/** Determine whether a setting exists. @since 1.0.0 */
	public static function has( string $key ): bool {
		return SettingsManager::has( $key );
	}

	/** Get a settings group. @since 1.0.0 */
	public static function get_group( string $group, ?array $default = null ): ?array {
		return SettingsManager::get_group( $group ) ?? $default;
	}

	/** Set a settings group. @since 1.0.0 */
	public static function set_group( string $group, array $settings ): bool {
		return SettingsManager::set_group( $group, $settings );
	}

	/** Register a settings group. @since 1.0.0 */
	public static function register_group( string $group, array $defaults = array() ): bool {
		return SettingsManager::register_group( $group, $defaults );
	}

	/** Register a settings key. @since 1.0.0 */
	public static function register_key( string $key, string $group, $default = null ): bool {
		return SettingsManager::register_key( $key, $group, $default );
	}

	/** Get all settings. @since 1.0.0 */
	public static function get_all(): array {
		return SettingsManager::get_all();
	}
}
