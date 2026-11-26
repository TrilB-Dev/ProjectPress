<?php
/**
 * Project plan read service.
 *
 * @package ProjectPress\Includes\Project\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Core;

use ProjectPress\Includes\Project\Requirements\RequirementRepository;
use ProjectPress\Includes\Project\Work\FeatureRepository;
use ProjectPress\Includes\Project\Work\MilestoneRepository;
use ProjectPress\Includes\Project\Work\TaskRepository;
use ProjectPress\Includes\Project\Work\UserStoryRepository;

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Builds project planning aggregates from canonical repositories.
 *
 * @since 1.0.0
 */
final class ProjectPlanService {
    /**
     * Project repository.
     *
     * @var ProjectRepository
     * @since 1.0.0
     */
    private ProjectRepository $projects;

    /**
     * Project permissions.
     *
     * @var ProjectPermissions
     * @since 1.0.0
     */
    private ProjectPermissions $permissions;

    /**
     * Milestone repository.
     *
     * @var MilestoneRepository
     * @since 1.0.0
     */
    private MilestoneRepository $milestones;

    /**
     * Task repository.
     *
     * @var TaskRepository
     * @since 1.0.0
     */
    private TaskRepository $tasks;

    /**
     * Requirement repository.
     *
     * @var RequirementRepository
     * @since 1.0.0
     */
    private RequirementRepository $requirements;

    /**
     * Feature repository.
     *
     * @var FeatureRepository
     * @since 1.0.0
     */
    private FeatureRepository $features;

    /**
     * User story repository.
     *
     * @var UserStoryRepository
     * @since 1.0.0
     */
    private UserStoryRepository $stories;

    /**
     * Initialise the project plan service.
     *
     * @param ProjectRepository      $projects Project repository.
     * @param ProjectPermissions     $permissions Project permissions.
     * @param MilestoneRepository    $milestones Milestone repository.
     * @param TaskRepository         $tasks Task repository.
     * @param RequirementRepository|null $requirements Requirement repository.
     * @param FeatureRepository|null    $features Feature repository.
     * @param UserStoryRepository|null  $stories User story repository.
     * @since 1.0.0
     */
    public function __construct(
        ProjectRepository $projects,
        ProjectPermissions $permissions,
        MilestoneRepository $milestones,
        TaskRepository $tasks,
        ?RequirementRepository $requirements = null,
        ?FeatureRepository $features = null,
        ?UserStoryRepository $stories = null,
    ) {
        $this->projects = $projects;
        $this->permissions = $permissions;
        $this->milestones = $milestones;
        $this->tasks = $tasks;
        $this->requirements = $requirements ?? new RequirementRepository();
        $this->features = $features ?? new FeatureRepository();
        $this->stories = $stories ?? new UserStoryRepository();
    }

    /**
     * Build a plan for a project.
     *
     * @param int $project_id Project ID.
     * @return ProjectPlan|null
     * @since 1.0.0
     */
    public function for_project(int $project_id): ?ProjectPlan {
        $project = $this->projects->find(absint($project_id));
        if (!$project || !$this->permissions->can_view($project)) {
            return null;
        }

        $id = $project->get_id();
        return new ProjectPlan(
            $id,
            $this->milestones->for_project($id),
            $this->tasks->for_project($id),
            $this->requirements->for_project($id),
            $this->features->for_project($id),
            $this->stories->for_project($id),
        );
    }
}
