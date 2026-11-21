<?php
namespace ProjectPress\Includes\Project\Composition;use ProjectPress\Includes\Project\Core\ProjectRecord;if(!defined('ABSPATH')){exit;}final class SBOM extends ProjectRecord{public function get_format():string{return(string)$this->get('format','');}public function get_components():array{return(array)$this->get('components',array());}}
