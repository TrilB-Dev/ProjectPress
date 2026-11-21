<?php
namespace ProjectPress\Includes\Project\Core;
if(!defined('ABSPATH')){exit;}
abstract class ProjectRecord{protected array $data;public function __construct(array $data=array()){$this->data=$data;}public function get_id():int{return absint($this->data['id']??0);}public function get_project_id():int{return absint($this->data['project_id']??0);}public function get(string $key,$default=null){return $this->data[$key]??$default;}public function to_array():array{return $this->data;}public function with(array $changes):static{return new static(array_merge($this->data,$changes));}}
