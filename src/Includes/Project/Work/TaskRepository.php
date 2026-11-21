<?php
namespace ProjectPress\Includes\Project\Work;
use ProjectPress\Includes\Functions\Helpers\DBHelper;
if(!defined('ABSPATH')){exit;}
final class TaskRepository{
 public function for_project(int $project_id):array{return array_map(static fn($row)=>new Task($row),DBHelper::get_all('tasks',array('project_id'=>absint($project_id)),'*','sort_order ASC, created_at ASC'));}
 public function find(int $id):?Task{$row=DBHelper::get_by_id('tasks',absint($id));return $row?new Task($row):null;}
 public function create(array $data){return DBHelper::insert('tasks',$data);}
 public function update(int $id,array $data):bool{return DBHelper::update('tasks',$data,array('id'=>absint($id)));}
 public function delete(int $id):bool{return DBHelper::delete('tasks',array('id'=>absint($id)));}
}
