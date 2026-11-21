<?php
namespace ProjectPress\Includes\Project\Delivery;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class Deployment extends ProjectRecord{public function get_environment_id():int{return absint($this->get('environment_id',0));}public function get_status():string{return(string)$this->get('status','pending');}}
