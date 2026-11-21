<?php
namespace ProjectPress\Includes\Project\Governance;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class DecisionLog extends ProjectRecord{public function get_title():string{return(string)$this->get('title','');}public function get_decision():string{return(string)$this->get('decision','');}}
