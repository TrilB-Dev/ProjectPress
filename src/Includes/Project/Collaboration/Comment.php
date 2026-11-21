<?php
namespace ProjectPress\Includes\Project\Collaboration;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class Comment extends ProjectRecord{public function get_content():string{return(string)$this->get('content','');}public function get_author_id():int{return absint($this->get('created_by',0));}}
