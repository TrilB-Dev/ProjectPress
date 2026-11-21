<?php
namespace ProjectPress\Includes\Project\Work;
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class Task {
 private array $data;
 public function __construct(array $data=array()){$this->data=$data;}
 public function get_id():int{return absint($this->data['id']??0);}
 public function get_project_id():int{return absint($this->data['project_id']??0);}
 public function get_title():string{return (string)($this->data['task_title']??'');}
 public function get_description():string{return (string)($this->data['task_description']??'');}
 public function get_status():string{return (string)($this->data['status']??'todo');}
 public function get_priority():string{return (string)($this->data['priority']??'normal');}
 public function get_assignee_id():?int{$id=absint($this->data['assignee_id']??0);return $id?:null;}
 public function get_due_date():?string{$v=(string)($this->data['due_date']??'');return ''===$v?null:$v;}
 public function to_array():array{return $this->data;}
}
