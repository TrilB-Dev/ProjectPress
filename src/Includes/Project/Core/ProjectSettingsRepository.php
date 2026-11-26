<?php
/**
 * Project settings persistence repository.
 *
 * @package ProjectPress\Includes\Project\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Core;

use ProjectPress\Includes\Functions\Helpers\DBHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reads, saves, and deletes project settings.
 *
 * @since 1.0.0
 */
final class ProjectSettingsRepository {
	/**
	 * Get settings for a project.
	 *
	 * @param int $project_id Project ID.
	 * @return array
	 * @since 1.0.0
	 */
	public function for_project( int $project_id ): array {
		return DBHelper::get_all( 'project_settings', array( 'project_id' => absint( $project_id ) ), '*', 'updated_at DESC' );
	}

	/**
	 * Save a project setting.
	 *
	 * @param int    $project_id Project ID.
	 * @param string $key Setting key.
	 * @param string $value Setting value.
	 * @return bool
	 * @since 1.0.0
	 */
	public function save( int $project_id, string $key, string $value ): bool {
		$existing = DBHelper::get_row( 'project_settings', array( 'project_id' => absint( $project_id ), 'setting_key' => sanitize_key( $key ) ) );
		$data = array(
			'setting_value' => sanitize_textarea_field( $value ),
			'updated_by' => get_current_user_id(),
			'updated_at' => current_time( 'mysql', true ),
		);

		if ( $existing ) {
			return DBHelper::update( 'project_settings', $data, array( 'id' => (int) $existing['id'] ) );
		}

		return false !== DBHelper::insert(
			'project_settings',
			array_merge(
				array( 'project_id' => absint( $project_id ), 'setting_key' => sanitize_key( $key ) ),
				$data
			)
		);
	}

	/**
	 * Delete a project setting.
	 *
	 * @param int $project_id Project ID.
	 * @param int $id Setting ID.
	 * @return bool
	 * @since 1.0.0
	 */
	public function delete( int $project_id, int $id ): bool {
		$row = DBHelper::get_by_id( 'project_settings', absint( $id ) );
		return $row
			&& absint( $row['project_id'] ) === absint( $project_id )
			&& DBHelper::delete( 'project_settings', array( 'id' => absint( $id ) ) );
	}
}
