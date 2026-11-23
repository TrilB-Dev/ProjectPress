<?php
namespace ProjectPress\Includes\Project\Governance;
use ProjectPress\Includes\Functions\Helpers\DBHelper;
if(!defined('ABSPATH')){exit;}
final class GovernanceRepository{
 private string $table;private string $class;
 public function __construct(string $type){$map=array('risk'=>array('risks',Risk::class),'issue'=>array('issues',Issue::class),'dependency'=>array('dependencies',Dependency::class),'decision'=>array('decisions',DecisionLog::class));$definition=$map[$type]??null;if(!$definition)throw new \InvalidArgumentException('Invalid governance record type.');$this->table=$definition[0];$this->class=$definition[1];}
 public function find(int$id){$row=DBHelper::get_by_id($this->table,absint($id));return is_array($row)?new $this->class($row):null;}
 public function for_project(int$project_id):array{$class=$this->class;return array_map(static fn(array$row)=>new $class($row),DBHelper::get_all($this->table,array('project_id'=>absint($project_id)),'*','created_at DESC'));}
 public function create(int$project_id,array$data){$now=current_time('mysql',true);$id=DBHelper::insert($this->table,array_merge($data,array('project_id'=>absint($project_id),'created_by'=>get_current_user_id(),'created_at'=>$now,'updated_by'=>get_current_user_id(),'updated_at'=>$now)));return false===$id?false:$this->find((int)$id);}
 public function update($record,array$data):bool{return DBHelper::update($this->table,array_merge($data,array('updated_by'=>get_current_user_id(),'updated_at'=>current_time('mysql',true))),array('id'=>$record->get_id(),'project_id'=>$record->get_project_id()));}
 public function delete($record):bool{return DBHelper::delete($this->table,array('id'=>$record->get_id(),'project_id'=>$record->get_project_id()));}
}
