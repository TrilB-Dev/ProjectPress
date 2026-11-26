<?php
/**
 * Project planning aggregate.
 *
 * @package ProjectPress\Includes\Project\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Core;

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Represents canonical planning data for a project.
 *
 * @since 1.0.0
 */
final class ProjectPlan {
    /**
     * Project ID.
     *
     * @var int
     * @since 1.0.0
     */
    private int $project_id;

    /**
     * Milestones.
     *
     * @var array
     * @since 1.0.0
     */
    private array $milestones;

    /**
     * Work items.
     *
     * @var array
     * @since 1.0.0
     */
    private array $work_items;

    /**
     * Requirements.
     *
     * @var array
     * @since 1.0.0
     */
    private array $requirements;

    /**
     * Features.
     *
     * @var array
     * @since 1.0.0
     */
    private array $features;

    /**
     * User stories.
     *
     * @var array
     * @since 1.0.0
     */
    private array $user_stories;

    /**
     * Initialise the project plan.
     *
     * @param int   $project_id Project ID.
     * @param array $milestones Milestones.
     * @param array $work_items Work items.
     * @param array $requirements Requirements.
     * @param array $features Features.
     * @param array $user_stories User stories.
     * @since 1.0.0
     */
    public function __construct(
        int $project_id,
        array $milestones = [],
        array $work_items = [],
        array $requirements = [],
        array $features = [],
        array $user_stories = [],
    ) {
        $this->project_id = absint($project_id);
        $this->milestones = array_values($milestones);
        $this->work_items = array_values($work_items);
        $this->requirements = array_values($requirements);
        $this->features = array_values($features);
        $this->user_stories = array_values($user_stories);
    }

    /**
     * Get the project ID.
     *
     * @return int
     * @since 1.0.0
     */
    public function get_project_id(): int {
        return $this->project_id;
    }

    /**
     * Get all milestones.
     *
     * @return array
     * @since 1.0.0
     */
    public function milestones(): array {
        return $this->milestones;
    }

    /**
     * Get all work items.
     *
     * @return array
     * @since 1.0.0
     */
    public function work_items(): array {
        return $this->work_items;
    }

    /**
     * Get all requirements.
     *
     * @return array
     * @since 1.0.0
     */
    public function requirements(): array {
        return $this->requirements;
    }

    /**
     * Get all features.
     *
     * @return array
     * @since 1.0.0
     */
    public function features(): array {
        return $this->features;
    }

    /**
     * Get all user stories.
     *
     * @return array
     * @since 1.0.0
     */
    public function user_stories(): array {
        return $this->user_stories;
    }

    /**
     * Get all planning items (milestones, requirements, features, user stories, work items).
     *
     * @return array
     * @since 1.0.0
     */
    public function planning_items(): array {
        return array_merge(
            $this->milestones,
            $this->requirements,
            $this->features,
            $this->user_stories,
            $this->work_items,
        );
    }

    /**
     * Add a milestone.
     *
     * @param mixed $item Milestone item.
     * @return self
     * @since 1.0.0
     */
    public function add_milestone($item): self {
        $this->milestones[] = $item;
        return $this;
    }

    /**
     * Add a work item.
     *
     * @param mixed $item Work item.
     * @return self
     * @since 1.0.0
     */
    public function add_work_item($item): self {
        $this->work_items[] = $item;
        return $this;
    }

    /**
     * Convert the plan to an array.
     *
     * @return array
     * @since 1.0.0
     */
    public function to_array(): array {
        return [
            "project_id" => $this->project_id,
            "milestones" => array_map([self::class, "normalise"], $this->milestones),
            "work_items" => array_map([self::class, "normalise"], $this->work_items),
            "requirements" => array_map([self::class, "normalise"], $this->requirements),
            "features" => array_map([self::class, "normalise"], $this->features),
            "user_stories" => array_map([self::class, "normalise"], $this->user_stories),
        ];
    }

    /**
     * Normalise a planning item.
     *
     * @param mixed $item Planning item.
     * @return array
     * @since 1.0.0
     */
    private static function normalise($item) {
        return is_object($item) && method_exists($item, "to_array") ? $item->to_array() : (array) $item;
    }
}
