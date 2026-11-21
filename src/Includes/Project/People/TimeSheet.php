<?php
namespace ProjectPress\Includes\Project\People;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class TimeSheet extends ProjectRecord{public function get_user_id():int{return absint($this->get('user_id',0));}public function get_minutes():int{return max(0,(int)$this->get('minutes',0));}}
