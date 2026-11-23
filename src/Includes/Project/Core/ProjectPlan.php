<?php
/** Project planning aggregate. @package ProjectPress */
namespace ProjectPress\Includes\Project\Core;
if(!defined('ABSPATH')){exit;}
final class ProjectPlan{
 private int $project_id;private array $milestones;private array $work_items;private array $requirements;private array $features;private array $user_stories;
 public function __construct(int $project_id,array $milestones=array(),array $work_items=array(),array $requirements=array(),array $features=array(),array $user_stories=array()){$this->project_id=absint($project_id);$this->milestones=array_values($milestones);$this->work_items=array_values($work_items);$this->requirements=array_values($requirements);$this->features=array_values($features);$this->user_stories=array_values($user_stories);}
 public function get_project_id():int{return$this->project_id;}public function milestones():array{return$this->milestones;}public function work_items():array{return$this->work_items;}public function requirements():array{return$this->requirements;}public function features():array{return$this->features;}public function user_stories():array{return$this->user_stories;}
 public function planning_items():array{return array_merge($this->milestones,$this->requirements,$this->features,$this->user_stories,$this->work_items);}
 public function add_milestone($item):self{$this->milestones[]=$item;return$this;}public function add_work_item($item):self{$this->work_items[]=$item;return$this;}
 public function to_array():array{return array('project_id'=>$this->project_id,'milestones'=>array_map(array(self::class,'normalise'),$this->milestones),'work_items'=>array_map(array(self::class,'normalise'),$this->work_items),'requirements'=>array_map(array(self::class,'normalise'),$this->requirements),'features'=>array_map(array(self::class,'normalise'),$this->features),'user_stories'=>array_map(array(self::class,'normalise'),$this->user_stories));}
 private static function normalise($item){return is_object($item)&&method_exists($item,'to_array')?$item->to_array():(array)$item;}
}
