<?php
/**
 * ProjectReportService for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Reports;

use ProjectPress\Includes\Project\Communication\NotificationRepository;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\ProjectRepository;
use ProjectPress\Includes\Project\Delivery\DeliveryRepository;
use ProjectPress\Includes\Project\Governance\GovernanceRepository;
use ProjectPress\Includes\Project\People\TimeSheetRepository;
use ProjectPress\Includes\Project\Quality\QualityRepository;
use ProjectPress\Includes\Project\Requirements\RequirementRepository;
use ProjectPress\Includes\Project\Work\FeatureRepository;
use ProjectPress\Includes\Project\Work\MilestoneRepository;
use ProjectPress\Includes\Project\Work\TaskRepository;
use ProjectPress\Includes\Project\Work\UserStoryRepository;
if (!defined("ABSPATH")) {
    exit();
}
/**
 * ProjectReportService implementation.
 *
 * @since 1.0.0
 */
final class ProjectReportService {
    /**
     * projects property.
     *
     * @var ProjectRepository
     * @since 1.0.0
     */
    private ProjectRepository $projects;
    /**
     * permissions property.
     *
     * @var ProjectPermissions
     * @since 1.0.0
     */
    private ProjectPermissions $permissions;
    /**
     * __construct.
     *
     * @param ProjectRepository $projects projects parameter.
     * @param ProjectPermissions $permissions permissions parameter.
     *
     * @since 1.0.0
     */
    public function __construct(ProjectRepository $projects, ProjectPermissions $permissions) {
        $this->projects = $projects;
        $this->permissions = $permissions;
    }
    /**
     * for_project.
     *
     * @param int $id id parameter.
     *
     * @return ?array for_project result.
     *
     * @since 1.0.0
     */
    public function for_project(int $id): ?array {
        $project = $this->projects->find(absint($id));
        if (!$project || !$this->permissions->can_view($project)) {
            return null;
        }
        $id = $project->get_id();
        $tasks = new TaskRepository()->for_project($id);
        $requirements = new RequirementRepository()->for_project($id);
        $features = new FeatureRepository()->for_project($id);
        $stories = new UserStoryRepository()->for_project($id);
        $milestones = new MilestoneRepository()->for_project($id);
        $risks = new GovernanceRepository("risk")->for_project($id);
        $issues = new GovernanceRepository("issue")->for_project($id);
        $bugs = new QualityRepository("bug")->for_project($id);
        $tests = new QualityRepository("test-case")->for_project($id);
        $sprints = new DeliveryRepository("sprint")->for_project($id);
        $releases = new DeliveryRepository("release")->for_project($id);
        $timesheets = new TimeSheetRepository()->for_project($id);
        $notifications = new NotificationRepository()->for_project($id);
        $minutes = array_sum(array_map(static fn($row) => $row->get_minutes(), $timesheets));
        return apply_filters(
            "projectpress_project_report",
            [
                "project" => $project->to_array(),
                "status" => [
                    "tasks" => Reports::count_by($tasks, "status"),
                    "requirements" => Reports::count_by($requirements, "status"),
                    "features" => Reports::count_by($features, "status"),
                    "user_stories" => Reports::count_by($stories, "status"),
                    "milestones" => Reports::count_by($milestones, "status"),
                    "risks" => Reports::count_by($risks, "status"),
                    "issues" => Reports::count_by($issues, "status"),
                    "bugs" => Reports::count_by($bugs, "status"),
                    "test_cases" => Reports::count_by($tests, "status"),
                    "sprints" => Reports::count_by($sprints, "status"),
                    "releases" => Reports::count_by($releases, "status"),
                ],
                "totals" => [
                    "tasks" => count($tasks),
                    "requirements" => count($requirements),
                    "features" => count($features),
                    "user_stories" => count($stories),
                    "milestones" => count($milestones),
                    "risks" => count($risks),
                    "issues" => count($issues),
                    "bugs" => count($bugs),
                    "test_cases" => count($tests),
                    "sprints" => count($sprints),
                    "releases" => count($releases),
                    "timesheet_minutes" => $minutes,
                    "notifications" => count($notifications),
                ],
            ],
            $project,
        );
    }
}
