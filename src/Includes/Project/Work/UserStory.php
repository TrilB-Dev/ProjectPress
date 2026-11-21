<?php
namespace ProjectPress\Includes\Project\Work;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class UserStory extends ProjectRecord{public function get_title():string{return(string)$this->get('title','');}public function get_points():int{return max(0,(int)$this->get('points',0));}}
