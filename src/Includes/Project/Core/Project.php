<?php
/**
 * A simple project aggregate for the ProjectPress domain layer.
 *
 * @package ProjectPress\Includes\Project\Core
 */

namespace ProjectPress\Includes\Project\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Project {
	/**
	 * Project status Draft.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public const STATUS_DRAFT = 'draft';
	/**
	 * Project status Pending.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public const STATUS_PENDING = 'pending';
	/**
	 * Project status On Hold.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public const STATUS_ON_HOLD = 'on_hold';
	/**
	 * Project status Cancelled.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public const STATUS_CANCELLED = 'cancelled';
	/**
	 * Project status In Review.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public const STATUS_IN_REVIEW = 'in_review';
	/**
	 * Project status Active.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public const STATUS_ACTIVE = 'active';
	/**
	 * Project status Archived.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public const STATUS_ARCHIVED = 'archived';
	/**
	 * Project status Completed.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public const STATUS_COMPLETED = 'completed';
	/**
	 * Project ID.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $id;
	/**
	 * Project title.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $title;
	/**
	 * Project description.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $description;
	/**
	 * Project status.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $status;
	/**
	 * Workspace ID associated with the project.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private string $workspace_id;
	/**
	 * Project metadata.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	private array $metadata;
	/**
	 * Project creation timestamp.
	 *
	 * @var \DateTimeImmutable
	 * @since 1.0.0
	 */
	private \DateTimeImmutable $created_at;
	/**
	 * Project last updated timestamp.
	 *
	 * @var \DateTimeImmutable
	 * @since 1.0.0
	 */
	private \DateTimeImmutable $updated_at;
	/**
	 * Project constructor.
	 *
	 * @param string $id
	 * @param string $title
	 * @param string $description
	 * @param string $status
	 * @param string $workspace_id
	 * @param array  $metadata
	 *
	 * @since 1.0.0
	 */
	public function __construct( string $id, string $title, string $description = '', string $status = self::STATUS_DRAFT, string $workspace_id = '', array $metadata = array() ) {
		$this->id = $id;
		$this->title = $title;
		$this->description = $description;
		$this->status = $this->normalize_status( $status );
		$this->workspace_id = $workspace_id;
		$this->metadata = $metadata;
		$this->created_at = new \DateTimeImmutable();
		$this->updated_at = new \DateTimeImmutable();
	}
	/**
	 * Get project ID.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_id(): string {
		return $this->id;
	}

	/**
	 * Get project title.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_title(): string {
		return $this->title;
	}

	/**
	 * Set project title.
	 *
	 * @param string $title
	 * @return self
	 * @since 1.0.0
	 */
	public function set_title( string $title ): self {
		$this->title = $title;
		$this->touch();
		return $this;
	}

	/**
	 * Get project description.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Set project description.
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
	 * Get project status.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_status(): string {
		return $this->status;
	}

	/**
	 * Set project status.
	 *
	 * @param string $status
	 * @return self
	 * @since 1.0.0
	 */
	public function set_status( string $status ): self {
		$this->status = $this->normalize_status( $status );
		$this->touch();
		return $this;
	}

	/**
	 * Check if project is active.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_active(): bool {
		return self::STATUS_ACTIVE === $this->status;
	}

	/**
	 * Check if project is completed.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_completed(): bool {
		return self::STATUS_COMPLETED === $this->status;
	}

	/**
	 * Check if project is archived.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_archived(): bool {
		return self::STATUS_ARCHIVED === $this->status;
	}
	/**
	 * Normalize the project status.
	 *
	 * @param string $status
	 * @return string
	 * @since 1.0.0
	 */
	private function normalize_status( string $status ): string {
		$allowed = array(
			self::STATUS_DRAFT,
			self::STATUS_PENDING,
			self::STATUS_ON_HOLD,
			self::STATUS_CANCELLED,
			self::STATUS_IN_REVIEW,
			self::STATUS_ACTIVE,
			self::STATUS_ARCHIVED,
			self::STATUS_COMPLETED,
		);

		$status = strtolower( trim( $status ) );

		return in_array( $status, $allowed, true ) ? $status : self::STATUS_DRAFT;
	}

	/**
	 * Get workspace ID.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_workspace_id(): string {
		return $this->workspace_id;
	}
	/**
	 * Get project metadata.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_metadata(): array {
		return $this->metadata;
	}
	/**
	 * Set project metadata.
	 *
	 * @param array $metadata
	 * @return self
	 * @since 1.0.0
	 */
	public function set_metadata( array $metadata ): self {
		$this->metadata = $metadata;
		$this->touch();
		return $this;
	}

	/**
	 * Set a single metadata value.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return self
	 * @since 1.0.0
	 */
	public function set_metadata_value( string $key, $value ): self {
		$this->metadata[ $key ] = $value;
		$this->touch();
		return $this;
	}

	/**
	 * Get a single metadata value.
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_metadata_value( string $key, $default = null ) {
		return $this->metadata[ $key ] ?? $default;
	}
	/**
	 * Get project creation date.
	 *
	 * @return \DateTimeImmutable
	 * @since 1.0.0
	 */
	public function get_created_at(): \DateTimeImmutable {
		return $this->created_at;
	}

	/**
	 * Get project last updated date.
	 *
	 * @return \DateTimeImmutable
	 * @since 1.0.0
	 */
	public function get_updated_at(): \DateTimeImmutable {
		return $this->updated_at;
	}
	/**
	 * Update the project's last updated date to the current time.
	 *
	 * @return self
	 * @since 1.0.0
	 */
	public function touch(): self {
		$this->updated_at = new \DateTimeImmutable();
		return $this;
	}
	/**
	 * Convert the project object to an array.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function to_array(): array {
		return array(
			'id' => $this->id,
			'title' => $this->title,
			'description' => $this->description,
			'status' => $this->status,
			'workspace_id' => $this->workspace_id,
			'metadata' => $this->metadata,
			'created_at' => $this->created_at->format( DATE_ATOM ),
			'updated_at' => $this->updated_at->format( DATE_ATOM ),
		);
	}
	/**
	 * Create a project object from an array.
	 *
	 * @param array $data
	 * @return self
	 * @since 1.0.0
	 */
	public static function from_array( array $data ): self {
		$project = new self(
			(string) ( $data['id'] ?? 'project-' . wp_unique_id() ),
			(string) ( $data['title'] ?? '' ),
			(string) ( $data['description'] ?? '' ),
			(string) ( $data['status'] ?? self::STATUS_DRAFT ),
			(string) ( $data['workspace_id'] ?? '' ),
			is_array( $data['metadata'] ?? null ) ? $data['metadata'] : array()
		);

		if ( ! empty( $data['created_at'] ) ) {
			$project->created_at = new \DateTimeImmutable( (string) $data['created_at'] );
		}

		if ( ! empty( $data['updated_at'] ) ) {
			$project->updated_at = new \DateTimeImmutable( (string) $data['updated_at'] );
		}

		return $project;
	}
}
