<?php
namespace ProjectPress\Includes\Project\Delivery;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class Environment extends ProjectRecord{public function get_name():string{return(string)$this->get('name','');}public function get_type():string{return(string)$this->get('type','');}}
