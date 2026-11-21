<?php
/** High-level facade for the ProjectPress project domain. */
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
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class ProjectManager {
 private static ?self $instance=null; private ProjectRepository $repository; private ProjectService $service; private ProjectContext $context; private ProjectPlanService $plans;
 private function __construct(){ $permissions=new ProjectPermissions(); $this->repository=new ProjectRepository(); $this->service=new ProjectService($this->repository,new ProjectValidator(),$permissions); $this->context=new ProjectContext($this->repository,$permissions); $this->plans=new ProjectPlanService($this->repository,$permissions,new MilestoneRepository(),new TaskRepository()); }
 public static function get_instance():self{return self::$instance??=new self();}
 public function repository():ProjectRepository{return $this->repository;} public function service():ProjectService{return $this->service;} public function context():ProjectContext{return $this->context;} public function plans():ProjectPlanService{return $this->plans;}
 public function find(int $id){return $this->repository->find($id);} public function query(array $arguments=array()):array{return $this->repository->query(new ProjectQuery($arguments));} public function count(array $arguments=array()):int{return $this->repository->count(new ProjectQuery($arguments));} public function create(array $data){return $this->service->create($data);} public function resolve($identifier){return $this->context->resolve($identifier);} public function plan(int $project_id):?ProjectPlan{return $this->plans->for_project($project_id);}
}
