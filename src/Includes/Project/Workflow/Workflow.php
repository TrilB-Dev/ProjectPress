<?php
/**
 * Workflow for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Workflow;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Workflow implementation.
 *
 * @since 1.0.0
 */
final class Workflow {
    /**
     * transitions property.
     *
     * @var array
     * @since 1.0.0
     */
    private array $transitions;
    public function __construct(array $transitions = []) {
        $this->transitions = $transitions;
    }
    /**
     * project_work.
     *
     * @return self project_work result.
     *
     * @since 1.0.0
     */
    public static function project_work(): self {
        return new self([
            "draft" => ["approved", "rejected"],
            "approved" => ["in_progress", "rejected"],
            "planned" => ["in_progress", "cancelled"],
            "backlog" => ["ready", "in_progress", "cancelled"],
            "ready" => ["in_progress", "cancelled"],
            "todo" => ["in_progress", "done"],
            "in_progress" => ["completed", "done", "cancelled"],
            "completed" => [],
            "done" => [],
            "cancelled" => [],
            "rejected" => [],
        ]);
    }
    /**
     * can_transition.
     *
     * @param string $from from parameter.
     * @param string $to to parameter.
     *
     * @return bool can_transition result.
     *
     * @since 1.0.0
     */
    public function can_transition(string $from, string $to): bool {
        return in_array($to, (array) ($this->transitions[$from] ?? []), true);
    }
    /**
     * transitions_from.
     *
     * @param string $status status parameter.
     *
     * @return array transitions_from result.
     *
     * @since 1.0.0
     */
    public function transitions_from(string $status): array {
        return array_values((array) ($this->transitions[$status] ?? []));
    }
    /**
     * statuses.
     *
     * @return array statuses result.
     *
     * @since 1.0.0
     */
    public function statuses(): array {
        return array_keys($this->transitions);
    }
}
