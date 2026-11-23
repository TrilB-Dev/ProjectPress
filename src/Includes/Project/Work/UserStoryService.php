<?php
namespace ProjectPress\Includes\Project\Work;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\ProjectRepository;
use WP_Error;
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class UserStoryService {
 private UserStoryRepository $stories;private ProjectRepository $projects;private ProjectPermissions $permissions;
 public function __construct(UserStoryRepository $stories,ProjectRepository $projects,ProjectPermissions $permissions){$this->stories=$stories;$this->projects=$projects;$this->permissions=$permissions;}
 public static function statuses():array{return array('backlog','ready','in_progress','completed','cancelled');}
 public function create(int $project_id,array $input){$project=$this->projects->find($project_id);if(!$project)return new WP_Error('projectpress_project_not_found',__('Project not found.','projectpress'));if(!$this->permissions->can_edit($project))return new WP_Error('projectpress_story_forbidden',__('You cannot change user stories for this project.','projectpress'));$data=$this->validate($input);return is_wp_error($data)?$data:$this->stories->create($project_id,$data);}
 public function update(UserStory $story,array $input){$project=$this->projects->find($story->get_project_id());if(!$project||!$this->permissions->can_edit($project))return new WP_Error('projectpress_story_forbidden',__('You cannot change user stories for this project.','projectpress'));$data=$this->validate($input);if(is_wp_error($data))return$data;return$this->stories->update($story,$data)?$this->stories->find($story->get_id()):false;}
 public function delete(UserStory $story){$project=$this->projects->find($story->get_project_id());if(!$project||!$this->permissions->can_edit($project))return new WP_Error('projectpress_story_forbidden',__('You cannot change user stories for this project.','projectpress'));return$this->stories->delete($story);}
 private function validate(array $input){$title=sanitize_text_field((string)($input['title']??''));if(''===$title)return new WP_Error('projectpress_story_title_required',__('A user story title is required.','projectpress'));$status=sanitize_key((string)($input['status']??'backlog'));if(!in_array($status,self::statuses(),true))return new WP_Error('projectpress_story_status_invalid',__('The user story status is invalid.','projectpress'));return array('title'=>$title,'description'=>sanitize_textarea_field((string)($input['description']??'')),'status'=>$status,'points'=>absint($input['points']??0),'sort_order'=>absint($input['sort_order']??0));}
}
