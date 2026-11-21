<?php
namespace ProjectPress\Includes\Project\Governance;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class Issue extends ProjectRecord{public function get_title():string{return(string)$this->get('title','');}public function get_status():string{return(string)$this->get('status','open');}}
