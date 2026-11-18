<?php
/**
 * Canonical ProjectPress default values.
 *
 * @package ProjectPress\Includes\Core
 */
namespace ProjectPress\Includes\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Defaults {
	public const PROJECT_STATUS_DEFAULT = 'planning';
	public const PROJECT_PRIORITY_DEFAULT = 'normal';
	public const PROJECT_VISIBILITY_DEFAULT = 'private';

	public static function project_statuses(): array {
		return array( 'planning', 'pending', 'active', 'on_hold', 'in_review', 'completed', 'cancelled', 'archived' );
	}

	public static function project_priorities(): array {
		return array( 'low', 'normal', 'high', 'urgent' );
	}

	public static function project_visibilities(): array {
		return array( 'private', 'team', 'public' );
	}

	public static function install_defaults(): void {
		// Reserved for idempotent installation data. Canonical values live above.
	}
}
