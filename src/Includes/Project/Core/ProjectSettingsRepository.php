<?php
namespace ProjectPress\Includes\Project\Core;
use ProjectPress\Includes\Functions\Helpers\DBHelper;
if(!defined('ABSPATH')){exit;}
final class ProjectSettingsRepository{
 public function for_project(int $project_id):array{return DBHelper::get_all('project_settings',array('project_id'=>absint($project_id)),'*','updated_at DESC');}
 public function save(int $project_id,string $key,string $value):bool{$existing=DBHelper::get_row('project_settings',array('project_id'=>absint($project_id),'setting_key'=>sanitize_key($key)));$data=array('setting_value'=>sanitize_textarea_field($value),'updated_by'=>get_current_user_id(),'updated_at'=>current_time('mysql',true));if($existing)return DBHelper::update('project_settings',$data,array('id'=>(int)$existing['id']));return false!==DBHelper::insert('project_settings',array_merge(array('project_id'=>absint($project_id),'setting_key'=>sanitize_key($key)),$data));}
 public function delete(int $project_id,int $id):bool{$row=DBHelper::get_by_id('project_settings',absint($id));return$row&&absint($row['project_id'])===absint($project_id)&&DBHelper::delete('project_settings',array('id'=>absint($id)));}
}
