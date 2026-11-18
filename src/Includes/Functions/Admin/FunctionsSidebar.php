<?php
/** ProjectPress menu registration and sidebar definitions. */
namespace ProjectPress\Includes\Functions\Admin;

use ProjectPress\Admin\Admin;
use ProjectPress\Includes\Functions\Helpers\AMHelper;
use ProjectPress\Includes\Functions\Helpers\ASMHelper;
use ProjectPress\Includes\Functions\Helpers\LoggerHelper;
use ProjectPress\Includes\Plugins\AdminMenuProviderInterface;
use ProjectPress\Includes\Plugins\AdminSidebarProviderInterface;
use ProjectPress\Includes\Plugins\Plugins;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class FunctionsSidebar {
	public static function register_admin_menu( Admin $admin ): void {
		foreach ( self::core_wordpress_menus( $admin ) as $menu ) { self::register_wordpress_menu( $menu ); }
		foreach ( AMHelper::filter( self::plugin_wordpress_menus() ) as $menu ) { self::register_wordpress_menu( $menu ); }
	}

	public static function get_sidebar_groups(): array {
		$groups = self::core_sidebar_groups();
		$menus = ASMHelper::filter( self::plugin_sidebar_menus() );
		foreach ( $menus as $menu ) { if ( '' === self::parent_slug( $menu ) ) { self::add_sidebar_group( $groups, $menu ); } }
		foreach ( $menus as $menu ) { $parent = self::parent_slug( $menu ); if ( '' !== $parent ) { self::add_sidebar_item( $groups, $parent, $menu ); } }
		foreach ( $groups as $key => $group ) {
			$filtered = array();
			foreach ( $group['items'] as $item ) { if ( self::can_view_menu_item( sanitize_key( (string) ( $item['capability'] ?? '' ) ) ) ) { $filtered[] = $item; } }
			$groups[ $key ]['items'] = $filtered;
		}
		return array_filter( $groups, static fn( array $group ): bool => ! empty( $group['items'] ) );
	}

	public static function get_admin_sidebar_menu_page_url( string $slug ): string { return admin_url( 'admin.php?page=' . $slug ); }

	private static function core_wordpress_menus( Admin $admin ): array {
		return array(
			array( 'name' => __( 'ProjectPress', 'projectpress' ), 'slug' => 'projectpress', 'icon' => 'dashicons-book-alt', 'parent' => '', 'callback' => array( $admin, 'render_dashboard' ), 'capability' => 'projectpress_admin_view', 'position' => 30 ),
			array( 'name' => __( 'Dashboard', 'projectpress' ), 'slug' => 'projectpress', 'parent' => 'projectpress', 'callback' => array( $admin, 'render_dashboard' ), 'capability' => 'projectpress_admin_view' ),
			array( 'name' => __( 'Projects', 'projectpress' ), 'slug' => 'projectpress&group=project-manager&tab=projects', 'parent' => 'projectpress', 'callback' => array( $admin, 'render_projects' ), 'capability' => 'projectpress_projects_view' ),
			array( 'name' => __( 'Manage Mods', 'projectpress' ), 'slug' => 'projectpress&group=mod-manager&tab=dashboard', 'parent' => 'projectpress', 'callback' => array( $admin, 'render_mods' ), 'capability' => 'projectpress_admin_view' ),
			array( 'name' => __( 'Settings', 'projectpress' ), 'slug' => 'projectpress&group=settings&tab=general', 'parent' => 'projectpress', 'callback' => array( $admin, 'render_settings' ), 'capability' => 'projectpress_settings_general_view' ),
			array( 'name' => __( 'Tools', 'projectpress' ), 'slug' => 'projectpress&group=tools&tab=general', 'parent' => 'projectpress', 'callback' => array( $admin, 'render_tools' ), 'capability' => 'projectpress_tools_debug' ),
		);
	}

	private static function core_sidebar_groups(): array {
		return array(
			'project-manager' => array(
				'label' => __( 'Project Manager', 'projectpress' ), 'icon' => 'fa-solid fa-diagram-project', 'items' => array(
					array( 'label' => __( 'Projects', 'projectpress' ), 'icon' => 'fa-solid fa-list', 'link' => 'projectpress&group=project-manager&tab=projects', 'capability' => 'projectpress_projects_view' ),
					array( 'label' => __( 'New Project', 'projectpress' ), 'icon' => 'fa-solid fa-plus', 'link' => 'projectpress&group=project-manager&tab=new', 'capability' => 'projectpress_projects_create' ),
					array( 'label' => __( 'Types', 'projectpress' ), 'icon' => 'fa-solid fa-shapes', 'link' => 'projectpress&group=project-manager&tab=types', 'capability' => 'projectpress_projects_taxonomies' ),
					array( 'label' => __( 'Categories', 'projectpress' ), 'icon' => 'fa-solid fa-folder-tree', 'link' => 'projectpress&group=project-manager&tab=categories', 'capability' => 'projectpress_projects_taxonomies' ),
					array( 'label' => __( 'Tags', 'projectpress' ), 'icon' => 'fa-solid fa-tags', 'link' => 'projectpress&group=project-manager&tab=tags', 'capability' => 'projectpress_projects_taxonomies' ),
					array( 'label' => __( 'Labels', 'projectpress' ), 'icon' => 'fa-solid fa-tag', 'link' => 'projectpress&group=project-manager&tab=labels', 'capability' => 'projectpress_projects_taxonomies' ),
					array( 'label' => __( 'Teams', 'projectpress' ), 'icon' => 'fa-solid fa-users', 'link' => 'projectpress&group=project-manager&tab=teams', 'capability' => 'projectpress_teams_view' ),
					array( 'label' => __( 'Global Fields', 'projectpress' ), 'icon' => 'fa-solid fa-table-list', 'link' => 'projectpress&group=project-manager&tab=fields', 'capability' => 'projectpress_fields_view' ),
				),
			),
			'mod-manager' => array( 'label' => __( 'Manage Mods', 'projectpress' ), 'icon' => 'fa-solid fa-file-lines', 'items' => array(
				array( 'label' => __( 'Overview', 'projectpress' ), 'icon' => 'fa-solid fa-gauge', 'link' => 'projectpress&group=mod-manager&tab=dashboard', 'capability' => 'projectpress_manager_dashboard' ),
				array( 'label' => __( 'Mods', 'projectpress' ), 'icon' => 'fa-solid fa-cubes', 'link' => 'projectpress&group=mod-manager&tab=mods', 'capability' => 'projectpress_manager_mods_view' ),
			) ),
			'settings' => array( 'label' => __( 'Settings', 'projectpress' ), 'icon' => 'fa-solid fa-gear', 'items' => array(
				array( 'label' => __( 'General', 'projectpress' ), 'icon' => 'fa-solid fa-sliders', 'link' => 'projectpress&group=settings&tab=general', 'capability' => 'projectpress_settings_general_view' ),
				array( 'label' => __( 'Layout', 'projectpress' ), 'icon' => 'fa-solid fa-table-columns', 'link' => 'projectpress&group=settings&tab=layout', 'capability' => 'projectpress_settings_layout_view' ),
				array( 'label' => __( 'Access', 'projectpress' ), 'icon' => 'fa-solid fa-user-shield', 'link' => 'projectpress&group=settings&tab=access', 'capability' => 'projectpress_settings_access_view' ),
				array( 'label' => __( 'Plugins', 'projectpress' ), 'icon' => 'fa-solid fa-puzzle-piece', 'link' => 'projectpress&group=settings&tab=plugins', 'capability' => 'projectpress_settings_plugins_view' ),
			) ),
			'tools' => array( 'label' => __( 'Tools', 'projectpress' ), 'icon' => 'fa-solid fa-toolbox', 'items' => array(
				array( 'label' => __( 'General', 'projectpress' ), 'icon' => 'fa-solid fa-sliders', 'link' => 'projectpress&group=tools&tab=general', 'capability' => 'projectpress_tools_general' ),
				array( 'label' => __( 'Debug', 'projectpress' ), 'icon' => 'fa-solid fa-bug-slash', 'link' => 'projectpress&group=tools&tab=debug', 'capability' => 'projectpress_tools_debug' ),
				array( 'label' => __( 'Import', 'projectpress' ), 'icon' => 'fa-solid fa-file-import', 'link' => 'projectpress&group=tools&tab=import', 'capability' => 'projectpress_tools_import' ),
				array( 'label' => __( 'Export', 'projectpress' ), 'icon' => 'fa-solid fa-file-export', 'link' => 'projectpress&group=tools&tab=export', 'capability' => 'projectpress_tools_export' ),
				array( 'label' => __( 'Reset', 'projectpress' ), 'icon' => 'fa-solid fa-rotate-left', 'link' => 'projectpress&group=tools&tab=reset', 'capability' => 'projectpress_tools_reset' ),
			) ),
		);
	}

	private static function register_wordpress_menu( array $menu ): void {
		$callback=$menu['callback']??null;$raw=(string)($menu['slug']??'');$slug=self::menu_page_slug($raw);$name=(string)($menu['name']??'');$parent=self::admin_parent_slug((string)($menu['parent']??''));$cap=self::resolve_menu_capability(sanitize_key((string)($menu['capability']??'manage_options')));
		if(''===$slug||''===$name||!is_callable($callback)){LoggerHelper::write_log(sprintf('ProjectPress skipped menu registration for empty or invalid page: %s',$raw));return;}
		if(''===$parent){add_menu_page($name,$name,$cap,$slug,$callback,$menu['icon']??'dashicons-admin-generic',$menu['position']??null);return;}
		if($slug!==$parent){add_submenu_page($parent,$name,$name,$cap,$slug,$callback,$menu['position']??null);}
	}
	private static function plugin_wordpress_menus():array{$menus=array();foreach(Plugins::get_instance()->get_registered_plugins() as $plugin){if(!$plugin instanceof AdminMenuProviderInterface||!$plugin->is_active())continue;foreach($plugin->get_admin_menu() as $definition){if(is_array($definition))$menus[]=self::normalize_wordpress_menu($definition);}}return $menus;}
	private static function normalize_wordpress_menu(array $d):array{return array('name'=>$d['menu_title']??$d['page_title']??'','slug'=>$d['menu_slug']??'','icon'=>$d['icon']??'dashicons-admin-generic','parent'=>$d['parent']??'','callback'=>$d['callback']??null,'capability'=>$d['capability']??'manage_options','position'=>$d['position']??null);}
	private static function plugin_sidebar_menus():array{$menus=array();foreach(Plugins::get_instance()->get_registered_plugins() as $plugin){if(!$plugin instanceof AdminSidebarProviderInterface||!$plugin->is_active())continue;foreach($plugin->get_admin_sidebar() as $d){if(is_array($d))$menus[]=ASMHelper::define($d['label']??'',self::sidebar_slug($d),$d['icon']??'',$d['parent']??'',$d['capability']??'');}}return $menus;}
	private static function sidebar_slug(array $d):string{$page=(string)($d['page']??$d['slug']??'');$query=$d['query']??array();return is_array($query)&&$query?$page.'&'.http_build_query(array_filter($query,'is_scalar'),'','&',PHP_QUERY_RFC3986):$page;}
	private static function add_sidebar_group(array &$groups,array $menu):void{$slug=self::menu_slug($menu);if(''!==$slug)$groups[$slug]=array('label'=>(string)($menu['name']??''),'icon'=>(string)($menu['icon']??''),'items'=>array());}
	private static function add_sidebar_item(array &$groups,string $parent,array $menu):void{if(isset($groups[$parent]))$groups[$parent]['items'][]=array('label'=>(string)($menu['name']??''),'icon'=>(string)($menu['icon']??''),'link'=>(string)($menu['slug']??''),'capability'=>sanitize_key((string)($menu['capability']??'')));}
	private static function parent_slug(array $menu):string{return sanitize_key((string)($menu['parent']??''));}
	private static function menu_slug(array $menu):string{return sanitize_key((string)($menu['slug']??''));}
	private static function admin_parent_slug(string $parent):string{return (string)preg_replace('/[^a-z0-9._-]/','',strtolower(sanitize_text_field($parent)));}
	private static function menu_page_slug(string $slug):string{return trim($slug);}
	private static function can_view_menu_item(string $capability):bool{return ''===$capability||current_user_can('manage_options')||current_user_can($capability);}
	private static function resolve_menu_capability(string $capability):string{return current_user_can('manage_options')?'manage_options':(''===$capability?'manage_options':$capability);}
}
