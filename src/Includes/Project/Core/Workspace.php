<?php
/**
 * A simple workspace aggregate for the ProjectPress domain layer.
 *
 * @package ProjectPress
 * @subpackage Includes\Project\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Workspace {
	/**
	 * Workspace ID.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $id;
	/**
	 * Workspace name.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $name;
	/**
	 * Workspace slug.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $slug;
	/**
	 * Workspace description.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $description;
	/**
	 * Project IDs associated with the workspace.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private array $project_ids;
	/**
	 * Workspace creation timestamp.
	 *
	 * @var \DateTimeImmutable
	 * @since 1.0.0
	 */
	private \DateTimeImmutable $created_at;
	/**
	 * Workspace last updated timestamp.
	 *
	 * @var \DateTimeImmutable
	 * @since 1.0.0
	 */
	private \DateTimeImmutable $updated_at;

	public function __construct(
		string $id,
		string $name,
		string $slug = '',
		string $description = '',
		array $project_ids = array()
	) {
		$this->id = $id;
		$this->name = $name;
		$this->slug = '' !== $slug ? $slug : sanitize_title( $name );
		$this->description = $description;
		$this->project_ids = array_values( array_unique( $project_ids ) );
		$this->created_at = new \DateTimeImmutable();
		$this->updated_at = new \DateTimeImmutable();
	}
	/**
	 * Get workspace ID.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_id(): string {
		return $this->id;
	}

	/**
	 * Get workspace name.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Set workspace name.
	 *
	 * @param string $name
	 * @return self
	 * @since 1.0.0
	 */
	public function set_name( string $name ): self {
		$this->name = $name;
		$this->slug = '' !== $this->slug ? $this->slug : sanitize_title( $name );
		$this->touch();
		return $this;
	}

	/**
	 * Get workspace slug.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Set workspace slug.
	 *
	 * @param string $slug
	 * @return self
	 * @since 1.0.0
	 */
	public function set_slug( string $slug ): self {
		$this->slug = sanitize_title( $slug );
		$this->touch();
		return $this;
	}

	/**
	 * Get workspace description.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Set workspace description.
	 *
	 * @param string $description
	 * @return self
	 * @since 1.0.0
	 */
	public function set_description( string $description ): self {
		$this->description = $description;
		$this->touch();
		return $this;
	}

	/**
	 * Get project IDs associated with the workspace.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_project_ids(): array {
		return $this->project_ids;
	}

	/**
	 * Add a project ID to the workspace.
	 *
	 * @param string $project_id
	 * @return self
	 * @since 1.0.0
	 */
	public function add_project_id( string $project_id ): self {
		if ( '' === $project_id ) {
			return $this;
		}

		$this->project_ids[] = $project_id;
		$this->project_ids = array_values( array_unique( $this->project_ids ) );
		$this->touch();
		return $this;
	}

	/**
	 * Remove a project ID from the workspace.
	 *
	 * @param string $project_id
	 * @return self
	 * @since 1.0.0
	 */
	public function remove_project_id( string $project_id ): self {
		$this->project_ids = array_values(
			array_filter(
				$this->project_ids,
				static fn( string $id ): bool => $id !== $project_id
			)
		);
		$this->touch();
		return $this;
	}

	/**
	 * Get workspace creation timestamp.
	 *
	 * @return \DateTimeImmutable
	 * @since 1.0.0
	 */
	public function get_created_at(): \DateTimeImmutable {
		return $this->created_at;
	}

	/**
	 * Get workspace last updated timestamp.
	 *
	 * @return \DateTimeImmutable
	 * @since 1.0.0
	 */
	public function get_updated_at(): \DateTimeImmutable {
		return $this->updated_at;
	}

	/**
	 * Update the workspace's last updated timestamp.
	 *
	 * @return self
	 * @since 1.0.0
	 */
	public function touch(): self {
		$this->updated_at = new \DateTimeImmutable();
		return $this;
	}

	/**
	 * Convert the workspace object to an array.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function to_array(): array {
		return array(
			'id' => $this->id,
			'name' => $this->name,
			'slug' => $this->slug,
			'description' => $this->description,
			'project_ids' => $this->project_ids,
			'created_at' => $this->created_at->format( DATE_ATOM ),
			'updated_at' => $this->updated_at->format( DATE_ATOM ),
		);
	}
	/**
	 * Create a workspace object from an array.
	 *
	 * @param array $data
	 * @return self
	 * @since 1.0.0
	 */
	public static function from_array( array $data ): self {
		$workspace = new self(
			(string) ( $data['id'] ?? 'workspace-' . wp_unique_id() ),
			(string) ( $data['name'] ?? '' ),
			(string) ( $data['slug'] ?? '' ),
			(string) ( $data['description'] ?? '' ),
			isset( $data['project_ids'] ) && is_array( $data['project_ids'] ) ? $data['project_ids'] : array()
		);

		if ( ! empty( $data['created_at'] ) ) {
			$workspace->created_at = new \DateTimeImmutable( (string) $data['created_at'] );
		}

		if ( ! empty( $data['updated_at'] ) ) {
			$workspace->updated_at = new \DateTimeImmutable( (string) $data['updated_at'] );
		}

		return $workspace;
	}
}
