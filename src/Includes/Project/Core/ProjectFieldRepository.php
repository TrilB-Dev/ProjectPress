<?php
namespace ProjectPress\Includes\Project\Core;
use ProjectPress\Includes\Functions\Helpers\DBHelper;
if(!defined('ABSPATH')){exit;}
final class ProjectFieldRepository{
 public function all(bool $enabled_only=false):array{return DBHelper::get_all('project_fields',$enabled_only?array('is_enabled'=>1):array(),'*','sort_order ASC, field_name ASC');}
 public function find(int $id):?array{return DBHelper::get_by_id('project_fields',absint($id));}
 public function create(array $data){return DBHelper::insert('project_fields',$data);}
 public function update(int $id,array $data):bool{return DBHelper::update('project_fields',$data,array('id'=>absint($id)));}
 public function values(int $project_id):array{$rows=DBHelper::get_all('project_field_values',array('project_id'=>absint($project_id)));$values=array();foreach($rows as $row){$values[(int)$row['field_id']]=$row;}return $values;}
 public function value(int $project_id,int $field_id):?array{return DBHelper::get_row('project_field_values',array('project_id'=>absint($project_id),'field_id'=>absint($field_id)));}
 public function save_value(int $project_id,int $field_id,array $data):bool{$existing=$this->value($project_id,$field_id);$data=array_merge($data,array('updated_by'=>get_current_user_id(),'updated_at'=>current_time('mysql',true)));if($existing)return DBHelper::update('project_field_values',$data,array('id'=>(int)$existing['id']));$data=array_merge(array('project_id'=>$project_id,'field_id'=>$field_id,'created_at'=>current_time('mysql',true)),$data);return false!==DBHelper::insert('project_field_values',$data);}
 public function delete_value(int $project_id,int $field_id):bool{return DBHelper::delete('project_field_values',array('project_id'=>$project_id,'field_id'=>$field_id));}
}
