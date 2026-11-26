<?php
/**
 * Normalises and stores project query arguments.
 *
 * @package ProjectPress\Includes\Project\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Core;

use ProjectPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Represents a sanitised project repository query.
 *
 * @since 1.0.0
 */
final class ProjectQuery {
	/**
	 * Sanitised query arguments.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private array $arguments;

	/**
	 * Initialise and sanitise query arguments.
	 *
	 * @param array $arguments Query arguments.
	 * @since 1.0.0
	 */
	public function __construct( array $arguments = array() ) {
		$this->arguments = array(
			'status' => SanitizationHelper::key( $arguments['status'] ?? '' ),
			'priority' => SanitizationHelper::key( $arguments['priority'] ?? '' ),
			'visibility' => SanitizationHelper::key( $arguments['visibility'] ?? '' ),
			'owner_id' => SanitizationHelper::integer( $arguments['owner_id'] ?? 0 ),
			'parent_id' => SanitizationHelper::integer( $arguments['parent_id'] ?? 0 ),
			'project_type' => SanitizationHelper::integer( $arguments['project_type'] ?? 0 ),
			'project_category' => SanitizationHelper::integer( $arguments['project_category'] ?? 0 ),
			'project_tag' => SanitizationHelper::integer( $arguments['project_tag'] ?? 0 ),
			'project_label' => SanitizationHelper::integer( $arguments['project_label'] ?? 0 ),
			'search' => SanitizationHelper::text( $arguments['search'] ?? '' ),
			'order_by' => SanitizationHelper::key( $arguments['order_by'] ?? 'updated_at', 'updated_at' ),
			'order' => 'ASC' === strtoupper( (string) ( $arguments['order'] ?? 'DESC' ) ) ? 'ASC' : 'DESC',
			'per_page' => SanitizationHelper::integer_range( $arguments['per_page'] ?? 20, 1, 100, 20 ),
			'page' => SanitizationHelper::integer_range( $arguments['page'] ?? 1, 1, PHP_INT_MAX, 1 ),
		);
	}

	/**
	 * Get a query argument.
	 *
	 * @param string $key Argument key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get( string $key, $default = null ) {
		return $this->arguments[ $key ] ?? $default;
	}

	/**
	 * Get all query arguments.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function to_array(): array {
		return $this->arguments;
	}
}
