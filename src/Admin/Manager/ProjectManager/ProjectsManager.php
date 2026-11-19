<?php
namespace ProjectPress\Admin\Manager\ProjectManager;
use ProjectPress\Admin\Manager\Manager;
use ProjectPress\Assets\Assets;
use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;
if ( ! defined( 'ABSPATH' ) ) { exit; }
final class ProjectsManager extends Manager {
 private Projects $projects; private Categories $categories; private Tags $tags; private Types $types; private Labels $labels; private Teams $teams; private Fields $fields;
 public function __construct(){ $this->page='project-manager'; $domain=DomainProjectManager::get_instance(); $this->projects=new Projects($domain); $this->categories=new Categories(); $this->tags=new Tags(); $this->types=new Types(); $this->labels=new Labels(); $this->teams=new Teams(); $this->fields=new Fields(); $this->register_actions(); }
 private function register_actions():void{
  add_action('admin_post_projectpress_project_save',array($this->projects,'handle_save'));
  add_action('admin_post_projectpress_project_archive',array($this->projects,'handle_archive'));
  add_action('admin_post_projectpress_project_restore',array($this->projects,'handle_restore'));
  add_action('admin_post_projectpress_project_delete',array($this->projects,'handle_delete'));
  add_action('admin_post_projectpress_projects_bulk',array($this->projects,'handle_bulk'));
  add_action('admin_post_projectpress_classification_save',array($this->types,'handle_save'));
  add_action('admin_post_projectpress_team_save',array($this->teams,'handle_save'));
  add_action('admin_post_projectpress_team_member_save',array($this->teams,'handle_member_save'));
  add_action('admin_post_projectpress_team_project_save',array($this->teams,'handle_project_save'));
  add_action('admin_post_projectpress_field_save',array($this->fields,'handle_save'));
 }
 public function register_assets(Assets $assets):void{$this->register_page_assets($assets,array('projectpress'),'projects');}
 public function render(string $tab='',string $form=''):void{if(!current_user_can('manage_options')&&!current_user_can('projectpress_projects_view'))wp_die(esc_html__('You are not authorised to view Project Manager.','projectpress'));$tab=$this->normalize_route(''!==$tab?$tab:RequestHelper::get_key('tab','projects'),array_combine(array('projects','new','edit','categories','tags','types','labels','fields','teams'),array('projects','new','edit','categories','tags','types','labels','fields','teams')),'projects');$this->header(__('Project Manager','projectpress'));switch($tab){case'new':$this->projects->render_form();break;case'edit':$this->projects->render_form(RequestHelper::get_integer('project_id',0));break;case'categories':$this->categories->render();break;case'tags':$this->tags->render();break;case'types':$this->types->render();break;case'labels':$this->labels->render();break;case'fields':$this->fields->render();break;case'teams':$this->teams->render();break;default:$this->projects->render();}$this->footer();}
}
