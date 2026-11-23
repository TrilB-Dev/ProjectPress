<?php
namespace ProjectPress\Includes\Project\Work;
use ProjectPress\Includes\Functions\Helpers\DBHelper;
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class UserStoryRepository {
 public function find(int $id):?UserStory{$row=DBHelper::get_by_id('user_stories',absint($id));return is_array($row)?new UserStory($row):null;}
 public function for_project(int $project_id):array{return array_map(static fn(array $row):UserStory=>new UserStory($row),DBHelper::get_all('user_stories',array('project_id'=>absint($project_id)),'*','sort_order ASC,created_at ASC'));}
 public function create(int $project_id,array $data){$now=current_time('mysql',true);$id=DBHelper::insert('user_stories',array_merge($data,array('project_id'=>absint($project_id),'created_by'=>get_current_user_id(),'created_at'=>$now,'updated_by'=>get_current_user_id(),'updated_at'=>$now)));return false===$id?false:$this->find((int)$id);}
 public function update(UserStory $story,array $data):bool{return DBHelper::update('user_stories',array_merge($data,array('updated_by'=>get_current_user_id(),'updated_at'=>current_time('mysql',true))),array('id'=>$story->get_id(),'project_id'=>$story->get_project_id()));}
 public function delete(UserStory $story):bool{return DBHelper::delete('user_stories',array('id'=>$story->get_id(),'project_id'=>$story->get_project_id()));}
}
