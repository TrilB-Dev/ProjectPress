<?php
/** Project plan read service. @package ProjectPress */
namespace ProjectPress\Includes\Project\Core;
use ProjectPress\Includes\Project\Requirements\RequirementRepository;use ProjectPress\Includes\Project\Work\FeatureRepository;use ProjectPress\Includes\Project\Work\MilestoneRepository;use ProjectPress\Includes\Project\Work\TaskRepository;use ProjectPress\Includes\Project\Work\UserStoryRepository;
if(!defined('ABSPATH')){exit;}
final class ProjectPlanService{
 private ProjectRepository $projects;private ProjectPermissions $permissions;private MilestoneRepository $milestones;private TaskRepository $tasks;private RequirementRepository $requirements;private FeatureRepository $features;private UserStoryRepository $stories;
 public function __construct(ProjectRepository $projects,ProjectPermissions $permissions,MilestoneRepository $milestones,TaskRepository $tasks,?RequirementRepository $requirements=null,?FeatureRepository $features=null,?UserStoryRepository $stories=null){$this->projects=$projects;$this->permissions=$permissions;$this->milestones=$milestones;$this->tasks=$tasks;$this->requirements=$requirements??new RequirementRepository();$this->features=$features??new FeatureRepository();$this->stories=$stories??new UserStoryRepository();}
 public function for_project(int $project_id):?ProjectPlan{$project=$this->projects->find(absint($project_id));if(!$project||!$this->permissions->can_view($project))return null;$id=$project->get_id();return new ProjectPlan($id,$this->milestones->for_project($id),$this->tasks->for_project($id),$this->requirements->for_project($id),$this->features->for_project($id),$this->stories->for_project($id));}
}
