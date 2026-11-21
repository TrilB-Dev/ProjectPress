<?php
namespace ProjectPress\Includes\Project\People;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class Team extends ProjectRecord{public function get_name():string{return(string)$this->get('team_name',$this->get('name',''));}public function get_owner_id():int{return absint($this->get('owner_id',0));}}
