<?php
/**
 * Base record for ProjectPress domain entities.
 *
 * @package ProjectPress\Includes\Project\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides common project record access and array conversion.
 *
 * @since 1.0.0
 */
abstract class ProjectRecord {
	/**
	 * Record data.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected array $data;

	/**
	 * Initialise the record.
	 *
	 * @param array $data Record data.
	 * @since 1.0.0
	 */
	public function __construct( array $data = array() ) {
		$this->data = $data;
	}

	/**
	 * Get the record ID.
	 *
	 * @return int
	 * @since 1.0.0
	 */
	public function get_id(): int {
		return absint( $this->data['id'] ?? 0 );
	}

	/**
	 * Get the owning project ID.
	 *
	 * @return int
	 * @since 1.0.0
	 */
	public function get_project_id(): int {
		return absint( $this->data['project_id'] ?? 0 );
	}

	/**
	 * Get a record value.
	 *
	 * @param string $key Record key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get( string $key, $default = null ) {
		return $this->data[ $key ] ?? $default;
	}

	/**
	 * Convert the record to an array.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function to_array(): array {
		return $this->data;
	}

	/**
	 * Create a copy with changed values.
	 *
	 * @param array $changes Values to merge.
	 * @return static
	 * @since 1.0.0
	 */
	public function with( array $changes ): static {
		return new static( array_merge( $this->data, $changes ) );
	}
}
