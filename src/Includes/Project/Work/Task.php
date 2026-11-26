<?php

/**
 * Represents a ProjectPress task record.
 *
 * @package ProjectPress\Includes\Project\Work
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Work;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Task domain component.
 *
 * @since 1.0.0
 */
final class Task {
    /**
     * Data.
     *
     * @var array
     * @since 1.0.0
     */
    private array $data;
    public function __construct(array $data = []) {
        $this->data = $data;
    }
    /**
     * Get id.
     *
     * @return int
     * @since 1.0.0
     */
    public function get_id(): int {
        return absint($this->data["id"] ?? 0);
    }
    /**
     * Get project id.
     *
     * @return int
     * @since 1.0.0
     */
    public function get_project_id(): int {
        return absint($this->data["project_id"] ?? 0);
    }
    /**
     * Get title.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_title(): string {
        return (string) ($this->data["task_title"] ?? "");
    }
    /**
     * Get description.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_description(): string {
        return (string) ($this->data["task_description"] ?? "");
    }
    /**
     * Get status.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_status(): string {
        return (string) ($this->data["status"] ?? "todo");
    }
    /**
     * Get priority.
     *
     * @return string
     * @since 1.0.0
     */
    public function get_priority(): string {
        return (string) ($this->data["priority"] ?? "normal");
    }
    /**
     * Get assignee id.
     *
     * @return ?int
     * @since 1.0.0
     */
    public function get_assignee_id(): ?int {
        $id = absint($this->data["assignee_id"] ?? 0);
        return $id ?: null;
    }
    /**
     * Get due date.
     *
     * @return ?string
     * @since 1.0.0
     */
    public function get_due_date(): ?string {
        $v = (string) ($this->data["due_date"] ?? "");
        return "" === $v ? null : $v;
    }
    /**
     * To array.
     *
     * @return array
     * @since 1.0.0
     */
    public function to_array(): array {
        return $this->data;
    }
}
