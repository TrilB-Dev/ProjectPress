<?php
namespace ProjectPress\Includes\Project\Workflow;
if(!defined('ABSPATH')){exit;}
final class Workflow{
 private array $transitions;public function __construct(array $transitions=array()){$this->transitions=$transitions;}
 public static function project_work():self{return new self(array('draft'=>array('approved','rejected'),'approved'=>array('in_progress','rejected'),'planned'=>array('in_progress','cancelled'),'backlog'=>array('ready','in_progress','cancelled'),'ready'=>array('in_progress','cancelled'),'todo'=>array('in_progress','done'),'in_progress'=>array('completed','done','cancelled'),'completed'=>array(),'done'=>array(),'cancelled'=>array(),'rejected'=>array()));}
 public function can_transition(string $from,string $to):bool{return in_array($to,(array)($this->transitions[$from]??array()),true);}public function transitions_from(string $status):array{return array_values((array)($this->transitions[$status]??array()));}public function statuses():array{return array_keys($this->transitions);}
}
