<?php
namespace ProjectPress\Includes\Project\Requirements;
use ProjectPress\Includes\Functions\Helpers\DBHelper;
if(!defined('ABSPATH')){exit;}
final class RequirementRepository{
 public function find(int $id):?Requirement{$row=DBHelper::get_by_id('requirements',absint($id));return is_array($row)?new Requirement($row):null;}
 public function for_project(int $project_id):array{return array_map(static fn(array $row):Requirement=>new Requirement($row),DBHelper::get_all('requirements',array('project_id'=>absint($project_id)),'*','sort_order ASC,created_at ASC'));}
 public function create(int $project_id,array $data){$now=current_time('mysql',true);$id=DBHelper::insert('requirements',array('project_id'=>absint($project_id),'title'=>sanitize_text_field((string)($data['title']??'')),'description'=>sanitize_textarea_field((string)($data['description']??'')),'status'=>sanitize_key((string)($data['status']??'draft')),'priority'=>sanitize_key((string)($data['priority']??'normal')),'sort_order'=>absint($data['sort_order']??0),'created_by'=>get_current_user_id(),'created_at'=>$now,'updated_by'=>get_current_user_id(),'updated_at'=>$now));return false===$id?false:$this->find((int)$id);}
 public function update(Requirement $requirement,array $data):bool{return DBHelper::update('requirements',array_merge($data,array('updated_by'=>get_current_user_id(),'updated_at'=>current_time('mysql',true))),array('id'=>$requirement->get_id()));}
 public function delete(Requirement $requirement):bool{return DBHelper::delete('requirements',array('id'=>$requirement->get_id(),'project_id'=>$requirement->get_project_id()));}
}
