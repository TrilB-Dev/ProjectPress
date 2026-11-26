<?php
/**
 * High-level facade for the ProjectPress project domain.
 *
 * @package ProjectPress\Includes\Project
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project;

use ProjectPress\Includes\Project\Core\ProjectContext;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\ProjectPlan;
use ProjectPress\Includes\Project\Core\ProjectPlanService;
use ProjectPress\Includes\Project\Core\ProjectQuery;
use ProjectPress\Includes\Project\Core\ProjectRepository;
use ProjectPress\Includes\Project\Core\ProjectService;
use ProjectPress\Includes\Project\Core\ProjectValidator;
use ProjectPress\Includes\Project\Work\MilestoneRepository;
use ProjectPress\Includes\Project\Work\TaskRepository;

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Provides access to project repositories, services, context, and plans.
 *
 * @since 1.0.0
 */
final class ProjectManager {
    /**
     * Singleton instance.
     *
     * @var ProjectManager|null
     * @since 1.0.0
     */
    private static ?self $instance = null;

    /**
     * Project repository.
     *
     * @var ProjectRepository
     * @since 1.0.0
     */
    private ProjectRepository $repository;

    /**
     * Project service.
     *
     * @var ProjectService
     * @since 1.0.0
     */
    private ProjectService $service;

    /**
     * Project context resolver.
     *
     * @var ProjectContext
     * @since 1.0.0
     */
    private ProjectContext $context;

    /**
     * Project plan service.
     *
     * @var ProjectPlanService
     * @since 1.0.0
     */
    private ProjectPlanService $plans;

    /**
     * Initialise the project domain dependencies.
     *
     * @since 1.0.0
     */
    private function __construct() {
        $permissions = new ProjectPermissions();
        $this->repository = new ProjectRepository();
        $this->service = new ProjectService($this->repository, new ProjectValidator(), $permissions);
        $this->context = new ProjectContext($this->repository, $permissions);
        $this->plans = new ProjectPlanService(
            $this->repository,
            $permissions,
            new MilestoneRepository(),
            new TaskRepository(),
        );
    }

    /**
     * Get the singleton instance.
     *
     * @return self
     * @since 1.0.0
     */
    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    /**
     * Get the project repository.
     *
     * @return ProjectRepository
     * @since 1.0.0
     */
    public function repository(): ProjectRepository {
        return $this->repository;
    }

    /**
     * Get the project service.
     *
     * @return ProjectService
     * @since 1.0.0
     */
    public function service(): ProjectService {
        return $this->service;
    }

    /**
     * Get the project context resolver.
     *
     * @return ProjectContext
     * @since 1.0.0
     */
    public function context(): ProjectContext {
        return $this->context;
    }

    /**
     * Get the project plan service.
     *
     * @return ProjectPlanService
     * @since 1.0.0
     */
    public function plans(): ProjectPlanService {
        return $this->plans;
    }

    /**
     * Find a project by ID.
     *
     * @param int $id Project ID.
     * @return \ProjectPress\Includes\Project\Core\Project|null
     * @since 1.0.0
     */
    public function find(int $id) {
        return $this->repository->find($id);
    }

    /**
     * Query projects.
     *
     * @param array $arguments Query arguments.
     * @return array
     * @since 1.0.0
     */
    public function query(array $arguments = []): array {
        return $this->repository->query(new ProjectQuery($arguments));
    }

    /**
     * Count matching projects.
     *
     * @param array $arguments Query arguments.
     * @return int
     * @since 1.0.0
     */
    public function count(array $arguments = []): int {
        return $this->repository->count(new ProjectQuery($arguments));
    }

    /**
     * Create a project.
     *
     * @param array $data Project data.
     * @return \ProjectPress\Includes\Project\Core\Project|\WP_Error
     * @since 1.0.0
     */
    public function create(array $data) {
        return $this->service->create($data);
    }

    /**
     * Resolve and validate a project identifier.
     *
     * @param int|string $identifier Project ID, key, or slug.
     * @return \ProjectPress\Includes\Project\Core\Project|\WP_Error
     * @since 1.0.0
     */
    public function resolve($identifier) {
        return $this->context->resolve($identifier);
    }

    /**
     * Get a project plan.
     *
     * @param int $project_id Project ID.
     * @return ProjectPlan|null
     * @since 1.0.0
     */
    public function plan(int $project_id): ?ProjectPlan {
        return $this->plans->for_project($project_id);
    }
}
