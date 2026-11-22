<?php
namespace ProjectPress\Includes\Plugins\SPDX\Includes;
use ProjectPress\Includes\Plugins\SPDX\Includes\SBOM\Generator;
if(!defined('ABSPATH')){exit;}
final class Includes{
 private static ?self $instance=null;private Generator $generator;
 private function __construct(){$this->generator=new Generator();}
 public static function get_instance():self{return self::$instance??=new self();}
 public function init():void{add_filter('projectpress_project_workspace_tabs',array($this,'workspace_tabs'));add_action('projectpress_project_workspace_after_tab',array($this,'render_workspace'),10,2);}
 public function generator():Generator{return $this->generator;}
 public function workspace_tabs(array $tabs):array{$tabs['sbom']=__('SBOM','projectpress');return $tabs;}
 public function render_workspace($project,string $tab):void{if('sbom'!==$tab)return;$document=$this->generator->generate($project);?><div class="card card-body"><h2 class="h4"><?php esc_html_e('Software Bill of Materials','projectpress');?></h2><p class="text-secondary"><?php esc_html_e('SPDX support is active for this project. This initial view generates an SPDX JSON document from the project and its current ProjectPress composition data.','projectpress');?></p><textarea class="form-control font-monospace" rows="18" readonly><?php echo esc_textarea(wp_json_encode($document,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));?></textarea></div><?php }
}
