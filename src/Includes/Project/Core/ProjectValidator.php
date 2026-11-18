<?php
namespace ProjectPress\Includes\Project\Core;

use ProjectPress\Includes\Core\Defaults;
use ProjectPress\Includes\Functions\Helpers\SanitizationHelper;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class ProjectValidator {
	public function validate( array $input, bool $partial = false ) {
		$clean = array();
		if ( ! $partial || array_key_exists( 'name', $input ) ) {
			$clean['name'] = SanitizationHelper::text( $input['name'] ?? '' );
			if ( '' === $clean['name'] ) { return new WP_Error( 'projectpress_project_name_required', __( 'A project name is required.', 'projectpress' ) ); }
		}
		if ( array_key_exists( 'description', $input ) ) { $clean['description'] = SanitizationHelper::textarea( $input['description'] ); }
		if ( array_key_exists( 'slug', $input ) ) { $clean['slug'] = SanitizationHelper::slug( $input['slug'] ); }
		if ( array_key_exists( 'key', $input ) ) { $clean['key'] = strtoupper( SanitizationHelper::key( $input['key'] ) ); }
		foreach ( array( 'parent_id', 'owner_id', 'type_id' ) as $field ) {
			if ( array_key_exists( $field, $input ) ) { $clean[ $field ] = SanitizationHelper::integer( $input[ $field ] ) ?: null; }
		}
		$allowed = array(
			'status' => Defaults::project_statuses(),
			'priority' => Defaults::project_priorities(),
			'visibility' => Defaults::project_visibilities(),
		);
		$fallbacks = array(
			'status' => Defaults::PROJECT_STATUS_DEFAULT,
			'priority' => Defaults::PROJECT_PRIORITY_DEFAULT,
			'visibility' => Defaults::PROJECT_VISIBILITY_DEFAULT,
		);
		foreach ( $allowed as $field => $values ) {
			if ( array_key_exists( $field, $input ) || ! $partial ) {
				$value = SanitizationHelper::key( $input[ $field ] ?? $fallbacks[ $field ] );
				if ( ! in_array( $value, $values, true ) ) { return new WP_Error( 'projectpress_invalid_' . $field, sprintf( __( 'Invalid project %s.', 'projectpress' ), $field ) ); }
				$clean[ $field ] = $value;
			}
		}
		foreach ( array( 'start_date', 'target_end_date', 'actual_end_date' ) as $field ) {
			if ( array_key_exists( $field, $input ) ) {
				$value = SanitizationHelper::text( $input[ $field ] );
				if ( '' !== $value && false === strtotime( $value ) ) { return new WP_Error( 'projectpress_invalid_' . $field, __( 'An invalid project date was supplied.', 'projectpress' ) ); }
				$clean[ $field ] = '' === $value ? null : gmdate( 'Y-m-d H:i:s', strtotime( $value ) );
			}
		}
		return $clean;
	}
}
