<?php
/**
 * Project domain entity.
 *
 * @package ProjectPress\Includes\Project\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Core;

use DateTimeImmutable;
use ProjectPress\Includes\Core\Defaults;

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Represents a ProjectPress project record.
 *
 * @since 1.0.0
 */
final class Project {
    /**
     * Project ID.
     *
     * @var int
     * @since 1.0.0
     */
    private int $id;
    /**
     * Project name.
     *
     * @var string
     * @since 1.0.0
     */
    private string $name;
    /**
     * Project slug.
     *
     * @var string
     * @since 1.0.0
     */
    private string $slug;
    /**
     * Project key.
     *
     * @var string
     * @since 1.0.0
     */
    private string $key;
    /**
     * Project description.
     *
     * @var string
     * @since 1.0.0
     */
    private string $description;
    /**
     * Parent project ID.
     *
     * @var int|null
     * @since 1.0.0
     */
    private ?int $parent_id;
    /**
     * Owner ID.
     *
     * @var int|null
     * @since 1.0.0
     */
    private ?int $owner_id;
    /**
     * Project status.
     *
     * @var string
     * @since 1.0.0
     */
    private string $status;
    /**
     * Project priority.
     *
     * @var string
     * @since 1.0.0
     */
    private string $priority;
    /**
     * Project visibility.
     *
     * @var string
     * @since 1.0.0
     */
    private string $visibility;
    /**
     * Project start date.
     *
     * @var DateTimeImmutable|null
     * @since 1.0.0
     */
    private ?DateTimeImmutable $start_date;
    /**
     * Project target end date.
     *
     * @var DateTimeImmutable|null
     * @since 1.0.0
     */
    private ?DateTimeImmutable $target_end_date;
    /**
     * Project actual end date.
     *
     * @var DateTimeImmutable|null
     * @since 1.0.0
     */
    private ?DateTimeImmutable $actual_end_date;
    /**
     * Created by user ID.
     *
     * @var int|null
     * @since 1.0.0
     */
    private ?int $created_by;
    /**
     * Project creation date.
     *
     * @var DateTimeImmutable|null
     * @since 1.0.0
     */
    private ?DateTimeImmutable $created_at;
    /**
     * Updated by user ID.
     *
     * @var int|null
     * @since 1.0.0
     */
    private ?int $updated_by;
    /**
     * Project update date.
     *
     * @var DateTimeImmutable|null
     * @since 1.0.0
     */
    private ?DateTimeImmutable $updated_at;

    /**
     * Initialise a project entity.
     *
     * @param array $data Project data.
     * @since 1.0.0
     */
    public function __construct(array $data = []) {
        $this->id = absint($data["id"] ?? 0);
        $this->name = (string) ($data["name"] ?? ($data["project_name"] ?? ""));
        $this->slug = (string) ($data["slug"] ?? ($data["project_slug"] ?? ""));
        $this->key = (string) ($data["key"] ?? ($data["project_key"] ?? ""));
        $this->description = (string) ($data["description"] ?? ($data["project_description"] ?? ""));
        $this->parent_id = self::nullable_id($data["parent_id"] ?? null);
        $this->owner_id = self::nullable_id($data["owner_id"] ?? null);
        $this->status = (string) ($data["status"] ?? Defaults::PROJECT_STATUS_DEFAULT);
        $this->priority = (string) ($data["priority"] ?? Defaults::PROJECT_PRIORITY_DEFAULT);
        $this->visibility = (string) ($data["visibility"] ?? Defaults::PROJECT_VISIBILITY_DEFAULT);
        $this->start_date = self::date($data["start_date"] ?? null);
        $this->target_end_date = self::date($data["target_end_date"] ?? null);
        $this->actual_end_date = self::date($data["actual_end_date"] ?? null);
        $this->created_by = self::nullable_id($data["created_by"] ?? null);
        $this->created_at = self::date($data["created_at"] ?? null);
        $this->updated_by = self::nullable_id($data["updated_by"] ?? null);
        $this->updated_at = self::date($data["updated_at"] ?? null);
    }

    /**
     * Normalise an optional ID.
     *
     * @param mixed $value ID value.
     * @return int|null
     * @since 1.0.0
     */
    private static function nullable_id($value): ?int {
        $value = absint($value);
        return $value > 0 ? $value : null;
    }

    /**
     * Normalise an optional date.
     *
     * @param mixed $value Date value.
     * @return DateTimeImmutable|null
     * @since 1.0.0
     */
    private static function date($value): ?DateTimeImmutable {
        if ($value instanceof DateTimeImmutable) {
            return $value;
        }
        if (!is_string($value) || "" === trim($value)) {
            return null;
        }
        try {
            return new DateTimeImmutable($value);
        } catch (\Exception $error) {
            return null;
        }
    }

    /**
     * Get the project ID.
     *
     * @return int
     * @since 1.0.0
     */
    public function get_id(): int {
        return $this->id;
    }

    /**
     * Set the project ID.
     *
     * @param int $id Project ID.
     * @return self
     * @since 1.0.0
     */
    public function set_id(int $id): self {
        $this->id = absint($id);
        return $this;
    }

    /**
     * Get the project name.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_name(): string {
        return $this->name;
    }
    /**
     * Get the project title.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_title(): string {
        return $this->name;
    }
    /**
     * Get the project slug.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_slug(): string {
        return $this->slug;
    }
    /**
     * Get the project key.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_key(): string {
        return $this->key;
    }
    /**
     * Get the project description.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_description(): string {
        return $this->description;
    }
    /**
     * Get the parent project ID.
     *
     * @return int|null
     * @since 1.0.0
     */
    public function get_parent_id(): ?int {
        return $this->parent_id;
    }
    /**
     * Get the project owner ID.
     *
     * @return int|null
     * @since 1.0.0
     */
    public function get_owner_id(): ?int {
        return $this->owner_id;
    }
    /**
     * Get the project status.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_status(): string {
        return $this->status;
    }
    /**
     * Get the project priority.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_priority(): string {
        return $this->priority;
    }
    /**
     * Get the project visibility.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_visibility(): string {
        return $this->visibility;
    }
    /**
     * Get the project start date.
     *
     * @return DateTimeImmutable|null
     * @since 1.0.0
     */
    public function get_start_date(): ?DateTimeImmutable {
        return $this->start_date;
    }
    /**
     * Get the project target end date.
     *
     * @return DateTimeImmutable|null
     * @since 1.0.0
     */
    public function get_target_end_date(): ?DateTimeImmutable {
        return $this->target_end_date;
    }
    /**
     * Get the project actual end date.
     *
     * @return DateTimeImmutable|null
     * @since 1.0.0
     */
    public function get_actual_end_date(): ?DateTimeImmutable {
        return $this->actual_end_date;
    }
    /**
     * Get the ID of the user who created the project.
     *
     * @return int|null
     * @since 1.0.0
     */
    public function get_created_by(): ?int {
        return $this->created_by;
    }
    /**
     * Get the project creation date.
     *
     * @return DateTimeImmutable|null
     * @since 1.0.0
     */
    public function get_created_at(): ?DateTimeImmutable {
        return $this->created_at;
    }
    /**
     * Get the ID of the user who last updated the project.
     *
     * @return int|null
     * @since 1.0.0
     */
    public function get_updated_by(): ?int {
        return $this->updated_by;
    }
    /**
     * Get the date when the project was last updated.
     *
     * @return DateTimeImmutable|null
     * @since 1.0.0
     */
    public function get_updated_at(): ?DateTimeImmutable {
        return $this->updated_at;
    }

    /**
     * Convert the project to an array.
     *
     * @return array
     * @since 1.0.0
     */
    public function to_array(): array {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "key" => $this->key,
            "description" => $this->description,
            "parent_id" => $this->parent_id,
            "owner_id" => $this->owner_id,
            "status" => $this->status,
            "priority" => $this->priority,
            "visibility" => $this->visibility,
            "start_date" => $this->start_date ? $this->start_date->format("Y-m-d H:i:s") : null,
            "target_end_date" => $this->target_end_date ? $this->target_end_date->format("Y-m-d H:i:s") : null,
            "actual_end_date" => $this->actual_end_date ? $this->actual_end_date->format("Y-m-d H:i:s") : null,
            "created_by" => $this->created_by,
            "created_at" => $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null,
            "updated_by" => $this->updated_by,
            "updated_at" => $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null,
        ];
    }
}
