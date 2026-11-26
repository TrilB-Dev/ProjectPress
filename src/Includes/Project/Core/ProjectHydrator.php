<?php
/**
 * Hydrates project entities to and from persistence data.
 *
 * @package ProjectPress\Includes\Project\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Converts project table rows and project entities.
 *
 * @since 1.0.0
 */
final class ProjectHydrator {
	/**
	 * Hydrate a project entity.
	 *
	 * @param array $row Project row.
	 * @return Project
	 * @since 1.0.0
	 */
	public function hydrate( array $row ): Project {
		return new Project( $row );
	}

	/**
	 * Extract project table data.
	 *
	 * @param Project $project Project entity.
	 * @return array
	 * @since 1.0.0
	 */
	public function extract( Project $project ): array {
		$data = $project->to_array();

		return array(
			'project_name' => $data['name'],
			'project_slug' => $data['slug'],
			'project_key' => $data['key'],
			'project_description' => $data['description'],
			'parent_id' => $data['parent_id'],
			'owner_id' => $data['owner_id'],
			'status' => $data['status'],
			'priority' => $data['priority'],
			'visibility' => $data['visibility'],
			'start_date' => $data['start_date'],
			'target_end_date' => $data['target_end_date'],
			'actual_end_date' => $data['actual_end_date'],
			'created_by' => $data['created_by'],
			'updated_by' => $data['updated_by'],
		);
	}
}
