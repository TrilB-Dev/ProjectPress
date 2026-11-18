<?php
/**
 * Project aggregate representing a ProjectPress project record.
 *
 * @package ProjectPress\Includes\Project\Core
 */

namespace ProjectPress\Includes\Project\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Project {
	/**
	 * Project statuses.
	 * 
	 * @since 1.0.0
	 */
	public const STATUS_DRAFT = 'draft';
	/**
	 * Project status: Pending.
	 *
	 * @since 1.0.0
	 */
	public const STATUS_PENDING = 'pending';
	/**
	 * Project status: On Hold.
	 *
	 * @since 1.0.0
	 */
	public const STATUS_ON_HOLD = 'on_hold';
	/**
	 * Project status: Cancelled.
	 *
	 * @since 1.0.0
	 */
	public const STATUS_CANCELLED = 'cancelled';
	/**
	 * Project status: In Review.
	 *
	 * @since 1.0.0
	 */
	public const STATUS_IN_REVIEW = 'in_review';
	/**
	 * Project status: Active.
	 *
	 * @since 1.0.0
	 */
	public const STATUS_ACTIVE = 'active';
	/**
	 * Project status: Archived.
	 *
	 * @since 1.0.0
	 */
	public const STATUS_ARCHIVED = 'archived';
	/**
	 * Project status: Completed.
	 *
	 * @since 1.0.0
	 */
	public const STATUS_COMPLETED = 'completed';

	/**
	 * Project priority: Low.
	 *
	 * @since 1.0.0
	 */
	public const PRIORITY_LOW = 'low';
	/**
	 * Project priority: Normal.
	 *
	 * @since 1.0.0
	 */
	public const PRIORITY_NORMAL = 'normal';
	/**
	 * Project priority: High.
	 *
	 * @since 1.0.0
	 */
	public const PRIORITY_HIGH = 'high';
	/**
	 * Project priority: Urgent.
	 *
	 * @since 1.0.0
	 */
	public const PRIORITY_URGENT = 'urgent';

	/**
	 * Project visibility: Private.
	 *
	 * @since 1.0.0
	 */
	public const VISIBILITY_PRIVATE = 'private';
	/**
	 * Project visibility: Team.
	 *
	 * @since 1.0.0
	 */
	public const VISIBILITY_TEAM = 'team';
	/**
	 * Project visibility: Public.
	 *
	 * @since 1.0.0
	 */
	public const VISIBILITY_PUBLIC = 'public';
	/**
	 * Project ID.
	 *
	 * @since 1.0.0
	 */
	private string $id;
	/**
	 * Project slug.
	 *
	 * @since 1.0.0
	 */
	private string $slug;
	/**
	 * Project key.
	 *
	 * @since 1.0.0
	 */
	private string $key;
	/**
	 * Project title.
	 *
	 * @since 1.0.0
	 */
	private string $title;
	/**
	 * Project description.
	 *
	 * @since 1.0.0
	 */
	private string $description;
	/**
	 * Project parent ID.
	 *
	 * @since 1.0.0
	 */
	private ?string $parent_id;
	/**
	 * Project owner ID.
	 *
	 * @since 1.0.0
	 */
	private ?int $owner_id;
	/**
	 * Project type ID.
	 *
	 * @since 1.0.0
	 */
	private ?int $type_id;
	/**
	 * Project status.
	 *
	 * @since 1.0.0
	 */
	private string $status;
	/**
	 * Project priority.
	 *
	 * @since 1.0.0
	 */
	private string $priority;
	/**
	 * Project visibility.
	 *
	 * @since 1.0.0
	 */
	private string $visibility;
	/**
	 * Project workspace ID.
	 *
	 * @since 1.0.0
	 */
	private string $workspace_id;
	/**
	 * Project start date.
	 *
	 * @since 1.0.0
	 */
	private ?\DateTimeImmutable $start_date;
	/**
	 * Project target end date.
	 *
	 * @since 1.0.0
	 */
	private ?\DateTimeImmutable $target_end_date;
	/**
	 * Project actual end date.
	 *
	 * @since 1.0.0
	 */
	private ?\DateTimeImmutable $actual_end_date;
	/**
	 * Project created by.
	 *
	 * @since 1.0.0
	 */
	private ?int $created_by;
	/**
	 * Project updated by.
	 *
	 * @since 1.0.0
	 */
	private ?int $updated_by;
	/**
	 * Project metadata.
	 *
	 * @since 1.0.0
	 */
	private array $metadata;
	/**
	 * Project created at.
	 *
	 * @since 1.0.0
	 */
	private \DateTimeImmutable $created_at;
	/**
	 * Project updated at.
	 *
	 * @since 1.0.0
	 */
	private \DateTimeImmutable $updated_at;
	/**
	 * Project slug.
	 *
	 * @since 1.0.0
	 */
	private string $slug;
	
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct(
		string $id,
		string $title,
		string $description = '',
		string $status = self::STATUS_DRAFT,
		string $workspace_id = '',
		array $metadata = array(),
		string $slug = '',
		string $key = '',
		?string $parent_id = null,
		?int $owner_id = null,
		?int $type_id = null,
		string $priority = self::PRIORITY_NORMAL,
		string $visibility = self::VISIBILITY_PRIVATE,
		?\DateTimeImmutable $start_date = null,
		?\DateTimeImmutable $target_end_date = null,
		?\DateTimeImmutable $actual_end_date = null,
		?int $created_by = null,
		?int $updated_by = null
	) {
		$this->id = $id;
		$this->title = trim( $title );
		$this->description = $description;
		$this->status = $this->normalize_status( $status );
		$this->workspace_id = $workspace_id;
		$this->metadata = $metadata;
		$this->parent_id = $parent_id;
		$this->owner_id = $owner_id;
		$this->type_id = $type_id;
		$this->priority = $this->normalize_priority( $priority );
		$this->visibility = $this->normalize_visibility( $visibility );
		$this->slug = '' !== $slug ? $slug : sanitize_title( $this->title );
		$this->key = '' !== $key ? $key : self::generate_key( $this->title );
		$this->start_date = $start_date;
		$this->target_end_date = $target_end_date;
		$this->actual_end_date = $actual_end_date;
		$this->created_by = $created_by;
		$this->updated_by = $updated_by;
		$this->created_at = new \DateTimeImmutable();
		$this->updated_at = new \DateTimeImmutable();
	}
	/**
	 * Get valid statuses.
	 *
	 * @since 1.0.0
	 */
	public static function get_valid_statuses(): array {
		return array(
			self::STATUS_DRAFT,
			self::STATUS_PENDING,
			self::STATUS_ON_HOLD,
			self::STATUS_CANCELLED,
			self::STATUS_IN_REVIEW,
			self::STATUS_ACTIVE,
			self::STATUS_ARCHIVED,
			self::STATUS_COMPLETED,
		);
	}

	/**
	 * Get valid priorities.
	 *
	 * @since 1.0.0
	 */
	public static function get_valid_priorities(): array {
		return array(
			self::PRIORITY_LOW,
			self::PRIORITY_NORMAL,
			self::PRIORITY_HIGH,
			self::PRIORITY_URGENT,
		);
	}

	/**
	 * Get valid visibility values.
	 *
	 * @since 1.0.0
	 */
	public static function get_valid_visibility_values(): array {
		return array(
			self::VISIBILITY_PRIVATE,
			self::VISIBILITY_TEAM,
			self::VISIBILITY_PUBLIC,
		);
	}
	/**
	 * Generate a unique project key based on the title.
	 *
	 * @since 1.0.0
	 */
	public static function generate_key( string $title ): string {
		$base = strtolower( preg_replace( '/[^a-z0-9]+/i', '-', trim( $title ) ) );
		$base = trim( (string) $base, '-' );
		$base = '' !== $base ? $base : 'project';

		return substr( sanitize_key( $base ), 0, 24 ) . '-' . wp_unique_id();
	}
	/**
	 * Get the project ID.
	 *
	 * @since 1.0.0
	 */
	public function get_id(): string {
		return $this->id;
	}

	/**
	 * Get the project slug.
	 *
	 * @since 1.0.0
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Set the project slug.
	 *
	 * @since 1.0.0
	 */
	public function set_slug( string $slug ): self {
		$this->slug = sanitize_title( $slug );
		$this->touch();
		return $this;
	}

	/**
	 * Get the project key.
	 *
	 * @since 1.0.0
	 */
	public function get_key(): string {
		return $this->key;
	}

	/**
	 * Set the project key.
	 *
	 * @since 1.0.0
	 */
	public function set_key( string $key ): self {
		$this->key = sanitize_key( $key );
		$this->touch();
		return $this;
	}

	/**
	 * Get the project title.
	 *
	 * @since 1.0.0
	 */
	public function get_title(): string {
		return $this->title;
	}

	/**
	 * Set the project title.
	 *
	 * @since 1.0.0
	 */
	public function set_title( string $title ): self {
		$this->title = trim( $title );
		if ( '' === $this->slug ) {
			$this->slug = sanitize_title( $this->title );
		}
		if ( '' === $this->key ) {
			$this->key = self::generate_key( $this->title );
		}
		$this->touch();
		return $this;
	}

	/**
	 * Get the project description.
	 *
	 * @since 1.0.0
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Set the project description.
	 *
	 * @since 1.0.0
	 */
	public function set_description( string $description ): self {
		$this->description = $description;
		$this->touch();
		return $this;
	}

	/**
	 * Get the parent project ID.
	 *
	 * @since 1.0.0
	 */
	public function get_parent_id(): ?string {
		return $this->parent_id;
	}

	/**
	 * Set the parent project ID.
	 *
	 * @since 1.0.0
	 */
	public function set_parent_id( ?string $parent_id ): self {
		$this->parent_id = $parent_id;
		$this->touch();
		return $this;
	}

	/**
	 * Get the owner ID.
	 *
	 * @since 1.0.0
	 */
	public function get_owner_id(): ?int {
		return $this->owner_id;
	}

	/**
	 * Set the owner ID.
	 *
	 * @since 1.0.0
	 */
	public function set_owner_id( ?int $owner_id ): self {
		$this->owner_id = $owner_id;
		$this->touch();
		return $this;
	}

	/**
	 * Get the type ID.
	 *
	 * @since 1.0.0
	 */
	public function get_type_id(): ?int {
		return $this->type_id;
	}

	/**
	 * Set the type ID.
	 *
	 * @since 1.0.0
	 */
	public function set_type_id( ?int $type_id ): self {
		$this->type_id = $type_id;
		$this->touch();
		return $this;
	}

	/**
	 * Get the project status.
	 *
	 * @since 1.0.0
	 */
	public function get_status(): string {
		return $this->status;
	}

	/**
	 * Set the project status.
	 *
	 * @since 1.0.0
	 */
	public function set_status( string $status ): self {
		$this->status = $this->normalize_status( $status );
		$this->touch();
		return $this;
	}

	/**
	 * Check if the project is active.
	 *
	 * @since 1.0.0
	 */
	public function is_active(): bool {
		return self::STATUS_ACTIVE === $this->status;
	}

	/**
	 * Check if the project is completed.
	 *
	 * @since 1.0.0
	 */
	public function is_completed(): bool {
		return self::STATUS_COMPLETED === $this->status;
	}

	/**
	 * Check if the project is archived.
	 *
	 * @since 1.0.0
	 */
	public function is_archived(): bool {
		return self::STATUS_ARCHIVED === $this->status;
	}

	/**
	 * Get the project priority.
	 *
	 * @since 1.0.0
	 */
	public function get_priority(): string {
		return $this->priority;
	}

	/**
	 * Set the project priority.
	 *
	 * @since 1.0.0
	 */
	public function set_priority( string $priority ): self {
		$this->priority = $this->normalize_priority( $priority );
		$this->touch();
		return $this;
	}

	/**
	 * Get the project visibility.
	 *
	 * @since 1.0.0
	 */
	public function get_visibility(): string {
		return $this->visibility;
	}

	/**
	 * Set the project visibility.
	 *
	 * @since 1.0.0
	 */
	public function set_visibility( string $visibility ): self {
		$this->visibility = $this->normalize_visibility( $visibility );
		$this->touch();
		return $this;
	}

	/**
	 * Get the workspace ID.
	 *
	 * @since 1.0.0
	 */
	public function get_workspace_id(): string {
		return $this->workspace_id;
	}

	/**
	 * Set the workspace ID.
	 *
	 * @since 1.0.0
	 */
	public function set_workspace_id( string $workspace_id ): self {
		$this->workspace_id = $workspace_id;
		$this->touch();
		return $this;
	}

	/**
	 * Get the project start date.
	 *
	 * @since 1.0.0
	 */
	public function get_start_date(): ?\DateTimeImmutable {
		return $this->start_date;
	}

	/**
	 * Set the project start date.
	 *
	 * @since 1.0.0
	 */
	public function set_start_date( ?\DateTimeImmutable $start_date ): self {
		$this->start_date = $start_date;
		$this->touch();
		return $this;
	}

	/**
	 * Get the project target end date.
	 *
	 * @since 1.0.0
	 */
	public function get_target_end_date(): ?\DateTimeImmutable {
		return $this->target_end_date;
	}

	/**
	 * Set the project target end date.
	 *
	 * @since 1.0.0
	 */
	public function set_target_end_date( ?\DateTimeImmutable $target_end_date ): self {
		$this->target_end_date = $target_end_date;
		$this->touch();
		return $this;
	}

	/**
	 * Get the project actual end date.
	 *
	 * @since 1.0.0
	 */
	public function get_actual_end_date(): ?\DateTimeImmutable {
		return $this->actual_end_date;
	}

	/**
	 * Set the project actual end date.
	 *
	 * @since 1.0.0
	 */
	public function set_actual_end_date( ?\DateTimeImmutable $actual_end_date ): self {
		$this->actual_end_date = $actual_end_date;
		$this->touch();
		return $this;
	}

	/**
	 * Get the ID of the user who created the project.
	 *
	 * @since 1.0.0
	 */
	public function get_created_by(): ?int {
		return $this->created_by;
	}
	/**
	 * Set the ID of the user who created the project.
	 *
	 * @since 1.0.0
	 */
	public function set_created_by( ?int $created_by ): self {
		$this->created_by = $created_by;
		$this->touch();
		return $this;
	}

	/**
	 * Get the ID of the user who last updated the project.
	 *
	 * @since 1.0.0
	 */
	public function get_updated_by(): ?int {
		return $this->updated_by;
	}

	/**
	 * Set the ID of the user who last updated the project.
	 *
	 * @since 1.0.0
	 */
	public function set_updated_by( ?int $updated_by ): self {
		$this->updated_by = $updated_by;
		$this->touch();
		return $this;
	}

	/**
	 * Get the project metadata.
	 *
	 * @since 1.0.0
	 */
	public function get_metadata(): array {
		return $this->metadata;
	}

	/**
	 * Set the project metadata.
	 *
	 * @since 1.0.0
	 */
	public function set_metadata( array $metadata ): self {
		$this->metadata = $metadata;
		$this->touch();
		return $this;
	}

	/**
	 * Set a metadata value for the project.
	 *
	 * @since 1.0.0
	 */
	public function set_metadata_value( string $key, $value ): self {
		$this->metadata[ $key ] = $value;
		$this->touch();
		return $this;
	}

	/**
	 * Get a metadata value for the project.
	 *
	 * @since 1.0.0
	 */
	public function get_metadata_value( string $key, $default = null ) {
		return $this->metadata[ $key ] ?? $default;
	}

	/**
	 * Check if a metadata value exists for the project.
	 *
	 * @since 1.0.0
	 */
	public function has_metadata_value( string $key ): bool {
		return array_key_exists( $key, $this->metadata );
	}

	/**
	 * Remove a metadata value for the project.
	 *
	 * @since 1.0.0
	 */
	public function remove_metadata_value( string $key ): self {
		unset( $this->metadata[ $key ] );
		$this->touch();
		return $this;
	}

	/**
	 * Merge metadata for the project.
	 *
	 * @since 1.0.0
	 */
	public function merge_metadata( array $metadata ): self {
		$this->metadata = array_merge( $this->metadata, $metadata );
		$this->touch();
		return $this;
	}

	/**
	 * Check if the project has a specific status.
	 *
	 * @since 1.0.0
	 */
	public function is_status( string $status ): bool {
		return $this->status === $this->normalize_status( $status );
	}
	/**
	 * Get the creation date of the project.
	 *
	 * @since 1.0.0
	 */
	public function get_created_at(): \DateTimeImmutable {
		return $this->created_at;
	}

	/**
	 * Get the last updated date of the project.
	 *
	 * @since 1.0.0
	 */
	public function get_updated_at(): \DateTimeImmutable {
		return $this->updated_at;
	}

	/**
	 * Update the last updated date of the project to the current time.
	 *
	 * @since 1.0.0
	 */
	public function touch(): self {
		$this->updated_at = new \DateTimeImmutable();
		return $this;
	}

	/**
	 * Convert the project to an array representation.
	 *
	 * @since 1.0.0
	 */
	public function to_array(): array {
		return array(
			'id' => $this->id,
			'slug' => $this->slug,
			'key' => $this->key,
			'title' => $this->title,
			'description' => $this->description,
			'parent_id' => $this->parent_id,
			'owner_id' => $this->owner_id,
			'type_id' => $this->type_id,
			'status' => $this->status,
			'priority' => $this->priority,
			'visibility' => $this->visibility,
			'workspace_id' => $this->workspace_id,
			'start_date' => $this->start_date ? $this->start_date->format( DATE_ATOM ) : null,
			'target_end_date' => $this->target_end_date ? $this->target_end_date->format( DATE_ATOM ) : null,
			'actual_end_date' => $this->actual_end_date ? $this->actual_end_date->format( DATE_ATOM ) : null,
			'created_by' => $this->created_by,
			'updated_by' => $this->updated_by,
			'metadata' => $this->metadata,
			'created_at' => $this->created_at->format( DATE_ATOM ),
			'updated_at' => $this->updated_at->format( DATE_ATOM ),
		);
	}
	/**
	 * Create a project instance from an array representation.
	 *
	 * @since 1.0.0
	 */
	public static function from_array( array $data ): self {
		$project = new self(
			(string) ( $data['id'] ?? 'project-' . wp_unique_id() ),
			(string) ( $data['title'] ?? '' ),
			(string) ( $data['description'] ?? '' ),
			(string) ( $data['status'] ?? self::STATUS_DRAFT ),
			(string) ( $data['workspace_id'] ?? '' ),
			is_array( $data['metadata'] ?? null ) ? $data['metadata'] : array(),
			(string) ( $data['slug'] ?? '' ),
			(string) ( $data['key'] ?? '' ),
			isset( $data['parent_id'] ) ? (string) $data['parent_id'] : null,
			isset( $data['owner_id'] ) ? (int) $data['owner_id'] : null,
			isset( $data['type_id'] ) ? (int) $data['type_id'] : null,
			(string) ( $data['priority'] ?? self::PRIORITY_NORMAL ),
			(string) ( $data['visibility'] ?? self::VISIBILITY_PRIVATE ),
			! empty( $data['start_date'] ) ? new \DateTimeImmutable( (string) $data['start_date'] ) : null,
			! empty( $data['target_end_date'] ) ? new \DateTimeImmutable( (string) $data['target_end_date'] ) : null,
			! empty( $data['actual_end_date'] ) ? new \DateTimeImmutable( (string) $data['actual_end_date'] ) : null,
			isset( $data['created_by'] ) ? (int) $data['created_by'] : null,
			isset( $data['updated_by'] ) ? (int) $data['updated_by'] : null
		);

		if ( ! empty( $data['created_at'] ) ) {
			$project->created_at = new \DateTimeImmutable( (string) $data['created_at'] );
		}

		if ( ! empty( $data['updated_at'] ) ) {
			$project->updated_at = new \DateTimeImmutable( (string) $data['updated_at'] );
		}

		return $project;
	}
	/**
	 * Normalize the project status.
	 *
	 * @since 1.0.0
	 */
	private function normalize_status( string $status ): string {
		$status = strtolower( trim( $status ) );
		return in_array( $status, self::get_valid_statuses(), true ) ? $status : self::STATUS_DRAFT;
	}

	/**
	 * Normalize the project priority.
	 *
	 * @since 1.0.0
	 */
	private function normalize_priority( string $priority ): string {
		$priority = strtolower( trim( $priority ) );
		return in_array( $priority, self::get_valid_priorities(), true ) ? $priority : self::PRIORITY_NORMAL;
	}

	/**
	 * Normalize the project visibility.
	 *
	 * @since 1.0.0
	 */
	private function normalize_visibility( string $visibility ): string {
		$visibility = strtolower( trim( $visibility ) );
		return in_array( $visibility, self::get_valid_visibility_values(), true ) ? $visibility : self::VISIBILITY_PRIVATE;
	}
}
