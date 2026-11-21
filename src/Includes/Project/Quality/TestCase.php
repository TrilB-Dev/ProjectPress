<?php
namespace ProjectPress\Includes\Project\Quality;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class TestCase extends ProjectRecord{public function get_title():string{return(string)$this->get('title','');}public function get_status():string{return(string)$this->get('status','pending');}}
