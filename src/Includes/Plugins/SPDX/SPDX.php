<?php
/** SPDX integration plugin for ProjectPress. @package ProjectPress */
namespace ProjectPress\Includes\Plugins\SPDX;
use ProjectPress\Includes\Plugins\PluginInterface;
use ProjectPress\Includes\Plugins\SPDX\Includes\Includes;
if(!defined('ABSPATH')){exit;}
final class SPDX implements PluginInterface{
 private static ?self $instance=null;
 public function get_slug():string{return 'projectpress-spdx';}
 public function get_name():string{return 'SPDX';}
 public function get_version():string{return '0.1.0';}
 public function get_author():string{return 'TrilB.Dev Team';}
 public function get_author_uri():string{return 'https://<scrubbed-url-domain>';}
 public function get_description():string{return __('Provides SPDX SBOM generation for ProjectPress projects.','projectpress');}
 public function get_uri():string{return 'https://<scrubbed-url-domain>/collection/web-extension/wordpress/projectpress';}
 public function get_license():string{return 'GPL-2.0-or-later';}
 public function is_active():bool{return true;}
 private function __construct(){}
 public function init():void{Includes::get_instance()->init();}
 public static function get_instance():self{return self::$instance??=new self();}
}
