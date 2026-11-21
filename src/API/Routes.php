<?php
/**
 * ProjectPress REST API.
 *
 * @package ProjectPress
 */
namespace ProjectPress\API;

use ProjectPress\Includes\Core\Defaults;
use ProjectPress\Includes\Functions\Helpers\DBHelper;
use ProjectPress\Includes\Licence\LicenceManager;
use ProjectPress\Includes\Project\Core\Project;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\ProjectManager;
use ProjectPress\Includes\Project\Work\TaskRepository;
use ProjectPress\Includes\Project\Work\TaskService;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Routes {
	public const NAMESPACE = 'projectpress/v1';
	private const RELATED = array(
		'milestones'  => 'milestones',
		'discussions' => 'discussions',
		'notes'       => 'notes',
		'files'       => 'files',
		'activity'    => 'activity',
		'settings'    => 'project_settings',
	);
	private static ?ProjectManager $projects = null;
	private static ?ProjectPermissions $permissions = null;

	public static function register_routes(): void {
		register_rest_route( self::NAMESPACE, '/licence/validate', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( self::class, 'validate_licence' ), 'permission_callback' => '__return_true' ) );
		register_rest_route( self::NAMESPACE, '/projects', array(
			array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( self::class, 'projects_index' ), 'permission_callback' => array( self::class, 'can_list_projects' ), 'args' => self::collection_args() ),
			array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( self::class, 'project_create' ), 'permission_callback' => array( self::class, 'can_create_project' ), 'args' => self::project_write_args( false ) ),
		) );
		register_rest_route( self::NAMESPACE, '/projects/(?P<project>[A-Za-z0-9_-]+)', array(
			array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( self::class, 'project_read' ), 'permission_callback' => array( self::class, 'can_read_project' ) ),
			array( 'methods' => WP_REST_Server::EDITABLE, 'callback' => array( self::class, 'project_update' ), 'permission_callback' => array( self::class, 'can_edit_project' ), 'args' => self::project_write_args( true ) ),
			array( 'methods' => WP_REST_Server::DELETABLE, 'callback' => array( self::class, 'project_delete' ), 'permission_callback' => array( self::class, 'can_delete_project' ) ),
		) );
		register_rest_route( self::NAMESPACE, '/projects/(?P<project>[A-Za-z0-9_-]+)/archive', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( self::class, 'project_archive' ), 'permission_callback' => array( self::class, 'can_edit_project' ) ) );
		register_rest_route( self::NAMESPACE, '/projects/(?P<project>[A-Za-z0-9_-]+)/restore', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( self::class, 'project_restore' ), 'permission_callback' => array( self::class, 'can_edit_project' ) ) );
		register_rest_route( self::NAMESPACE, '/projects/(?P<project>[A-Za-z0-9_-]+)/tasks', array(
			array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( self::class, 'tasks_index' ), 'permission_callback' => array( self::class, 'can_read_project' ) ),
			array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( self::class, 'task_create' ), 'permission_callback' => array( self::class, 'can_edit_project' ), 'args' => self::task_args() ),
		) );
		register_rest_route( self::NAMESPACE, '/projects/(?P<project>[A-Za-z0-9_-]+)/tasks/(?P<id>\d+)', array(
			array( 'methods' => WP_REST_Server::EDITABLE, 'callback' => array( self::class, 'task_update' ), 'permission_callback' => array( self::class, 'can_edit_project' ), 'args' => self::task_args() ),
			array( 'methods' => WP_REST_Server::DELETABLE, 'callback' => array( self::class, 'task_delete' ), 'permission_callback' => array( self::class, 'can_edit_project' ) ),
		) );
		register_rest_route( self::NAMESPACE, '/projects/(?P<project>[A-Za-z0-9_-]+)/(?P<entity>milestones|discussions|notes|files|activity|settings)', array(
			array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( self::class, 'related_index' ), 'permission_callback' => array( self::class, 'can_read_project' ) ),
			array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( self::class, 'related_create' ), 'permission_callback' => array( self::class, 'can_edit_project' ) ),
		) );
		register_rest_route( self::NAMESPACE, '/projects/(?P<project>[A-Za-z0-9_-]+)/(?P<entity>milestones|discussions|notes|files|settings)/(?P<id>\d+)', array( 'methods' => WP_REST_Server::DELETABLE, 'callback' => array( self::class, 'related_delete' ), 'permission_callback' => array( self::class, 'can_edit_project' ) ) );
	}

	public static function projects_index( WP_REST_Request $request ): WP_REST_Response {
		$args = array_intersect_key( $request->get_params(), array_flip( array_keys( self::collection_args() ) ) );
		$items = array_map( array( self::class, 'project_data' ), self::projects()->query( $args ) );
		return self::success( $items, 200, array( 'total' => self::projects()->count( $args ), 'page' => absint( $args['page'] ?? 1 ), 'per_page' => absint( $args['per_page'] ?? 20 ) ) );
	}
	public static function project_read( WP_REST_Request $request ) { $project = self::resolve_project( $request ); return is_wp_error( $project ) ? $project : self::success( self::project_data( $project ) ); }
	public static function project_create( WP_REST_Request $request ) { $result = self::projects()->create( self::project_input( $request ) ); return self::domain_response( $result, 201 ); }
	public static function project_update( WP_REST_Request $request ) { $project=self::resolve_project($request); if(is_wp_error($project))return $project; return self::domain_response(self::projects()->service()->update($project,self::project_input($request))); }
	public static function project_archive( WP_REST_Request $request ) { $project=self::resolve_project($request); return is_wp_error($project)?$project:self::domain_response(self::projects()->service()->archive($project)); }
	public static function project_restore( WP_REST_Request $request ) { $project=self::resolve_project($request); return is_wp_error($project)?$project:self::domain_response(self::projects()->service()->restore($project)); }
	public static function project_delete( WP_REST_Request $request ) { $project=self::resolve_project($request); if(is_wp_error($project))return $project;$result=self::projects()->service()->delete($project);return is_wp_error($result)?$result:self::success(array('deleted'=>true,'id'=>$project->get_id())); }

	public static function tasks_index( WP_REST_Request $request ) { $project=self::resolve_project($request);if(is_wp_error($project))return $project;$items=array_map(static fn($task)=>$task->to_array(),(new TaskRepository())->for_project($project->get_id()));return self::success($items); }
	public static function task_create( WP_REST_Request $request ) { $project=self::resolve_project($request);if(is_wp_error($project))return $project;$repo=new TaskRepository();$service=new TaskService($repo,self::projects()->repository(),self::permissions());return self::domain_response($service->create($project->get_id(),self::task_input($request)),201); }
	public static function task_update( WP_REST_Request $request ) { $project=self::resolve_project($request);if(is_wp_error($project))return $project;$repo=new TaskRepository();$task=$repo->find(absint($request['id']));if(!$task||$task->get_project_id()!==$project->get_id())return self::not_found(__('Task not found.','projectpress'));$service=new TaskService($repo,self::projects()->repository(),self::permissions());return self::domain_response($service->update($task,self::task_input($request))); }
	public static function task_delete( WP_REST_Request $request ) { $project=self::resolve_project($request);if(is_wp_error($project))return $project;$repo=new TaskRepository();$task=$repo->find(absint($request['id']));if(!$task||$task->get_project_id()!==$project->get_id())return self::not_found(__('Task not found.','projectpress'));$service=new TaskService($repo,self::projects()->repository(),self::permissions());$result=$service->delete($task);return is_wp_error($result)?$result:self::success(array('deleted'=>true,'id'=>$task->get_id())); }

	public static function related_index( WP_REST_Request $request ) { $project=self::resolve_project($request);if(is_wp_error($project))return $project;$entity=sanitize_key((string)$request['entity']);$table=self::RELATED[$entity]??'';if(''===$table)return self::not_found(__('Related entity not found.','projectpress'));$order='settings'===$entity?'updated_at DESC':'created_at DESC';return self::success(DBHelper::get_all($table,array('project_id'=>$project->get_id()),'*',$order)); }
	public static function related_create( WP_REST_Request $request ) { $project=self::resolve_project($request);if(is_wp_error($project))return $project;$entity=sanitize_key((string)$request['entity']);if('activity'===$entity)return new WP_Error('projectpress_rest_activity_read_only',__('Activity is read only.','projectpress'),array('status'=>405));$data=self::related_input($entity,$project->get_id(),$request);if(is_wp_error($data))return $data;$id=DBHelper::insert(self::RELATED[$entity],$data);return false===$id?new WP_Error('projectpress_rest_create_failed',__('The related record could not be created.','projectpress'),array('status'=>500)):self::success(DBHelper::get_by_id(self::RELATED[$entity],(int)$id),201); }
	public static function related_delete( WP_REST_Request $request ) { $project=self::resolve_project($request);if(is_wp_error($project))return $project;$entity=sanitize_key((string)$request['entity']);$table=self::RELATED[$entity]??'';$id=absint($request['id']);$row=$table?DBHelper::get_by_id($table,$id):null;if(!$row||(int)$row['project_id']!==$project->get_id())return self::not_found(__('Related record not found.','projectpress'));return DBHelper::delete($table,array('id'=>$id))?self::success(array('deleted'=>true,'id'=>$id)):new WP_Error('projectpress_rest_delete_failed',__('The related record could not be deleted.','projectpress'),array('status'=>500)); }

	public static function can_list_projects(){return current_user_can('manage_options')||current_user_can('projectpress_projects_view')?true:self::forbidden();}
	public static function can_create_project(){return self::permissions()->can_create()?true:self::forbidden();}
	public static function can_read_project(WP_REST_Request $request){$project=self::resolve_project($request);return is_wp_error($project)?$project:(self::permissions()->can_view($project)?true:self::forbidden());}
	public static function can_edit_project(WP_REST_Request $request){$project=self::resolve_project($request);return is_wp_error($project)?$project:(self::permissions()->can_edit($project)?true:self::forbidden());}
	public static function can_delete_project(WP_REST_Request $request){$project=self::resolve_project($request);return is_wp_error($project)?$project:(self::permissions()->can_delete($project)?true:self::forbidden());}

	private static function collection_args():array{return array('status'=>array('sanitize_callback'=>'sanitize_key'),'priority'=>array('sanitize_callback'=>'sanitize_key'),'visibility'=>array('sanitize_callback'=>'sanitize_key'),'owner_id'=>array('sanitize_callback'=>'absint'),'parent_id'=>array('sanitize_callback'=>'absint'),'project_type'=>array('sanitize_callback'=>'absint'),'project_category'=>array('sanitize_callback'=>'absint'),'project_tag'=>array('sanitize_callback'=>'absint'),'project_label'=>array('sanitize_callback'=>'absint'),'search'=>array('sanitize_callback'=>'sanitize_text_field'),'order_by'=>array('sanitize_callback'=>'sanitize_key'),'order'=>array('sanitize_callback'=>'sanitize_text_field'),'page'=>array('sanitize_callback'=>'absint'),'per_page'=>array('sanitize_callback'=>'absint'));}
	private static function project_write_args(bool $partial):array{return array('name'=>array('required'=>!$partial,'type'=>'string','sanitize_callback'=>'sanitize_text_field'),'description'=>array('type'=>'string','sanitize_callback'=>'sanitize_textarea_field'),'slug'=>array('type'=>'string','sanitize_callback'=>'sanitize_title'),'key'=>array('type'=>'string','sanitize_callback'=>'sanitize_key'),'owner_id'=>array('type'=>'integer','sanitize_callback'=>'absint'),'parent_id'=>array('type'=>'integer','sanitize_callback'=>'absint'),'status'=>array('type'=>'string','enum'=>Defaults::project_statuses(),'sanitize_callback'=>'sanitize_key'),'priority'=>array('type'=>'string','enum'=>Defaults::project_priorities(),'sanitize_callback'=>'sanitize_key'),'visibility'=>array('type'=>'string','enum'=>Defaults::project_visibilities(),'sanitize_callback'=>'sanitize_key'),'start_date'=>array('type'=>'string'),'target_end_date'=>array('type'=>'string'),'actual_end_date'=>array('type'=>'string'));}
	private static function task_args():array{return array('title'=>array('type'=>'string','sanitize_callback'=>'sanitize_text_field'),'description'=>array('type'=>'string','sanitize_callback'=>'sanitize_textarea_field'),'status'=>array('type'=>'string','enum'=>TaskService::statuses(),'sanitize_callback'=>'sanitize_key'),'priority'=>array('type'=>'string','enum'=>TaskService::priorities(),'sanitize_callback'=>'sanitize_key'),'assignee_id'=>array('type'=>'integer','sanitize_callback'=>'absint'),'due_date'=>array('type'=>'string'));}
	private static function project_input(WP_REST_Request $r):array{$keys=array_keys(self::project_write_args(true));return array_intersect_key($r->get_params(),array_flip($keys));}
	private static function task_input(WP_REST_Request $r):array{$keys=array_keys(self::task_args());return array_intersect_key($r->get_params(),array_flip($keys));}
	private static function related_input(string $entity,int $project_id,WP_REST_Request $r){$now=current_time('mysql',true);if('files'===$entity){$attachment=absint($r->get_param('attachment_id'));if('attachment'!==get_post_type($attachment))return new WP_Error('projectpress_rest_file_invalid',__('A valid media attachment is required.','projectpress'),array('status'=>400));return array('project_id'=>$project_id,'attachment_id'=>$attachment,'file_label'=>sanitize_text_field((string)$r->get_param('label')),'created_by'=>get_current_user_id(),'created_at'=>$now);}if('settings'===$entity){$key=sanitize_key((string)$r->get_param('key'));if(''===$key)return new WP_Error('projectpress_rest_setting_key_required',__('A setting key is required.','projectpress'),array('status'=>400));return array('project_id'=>$project_id,'setting_key'=>$key,'setting_value'=>sanitize_textarea_field((string)$r->get_param('value')),'updated_by'=>get_current_user_id(),'updated_at'=>$now);}if(!in_array($entity,array('milestones','discussions','notes'),true))return self::not_found(__('Related entity not found.','projectpress'));$title=sanitize_text_field((string)$r->get_param('title'));if(''===$title)return new WP_Error('projectpress_rest_title_required',__('A title is required.','projectpress'),array('status'=>400));$data=array('project_id'=>$project_id,'title'=>$title,'created_by'=>get_current_user_id(),'created_at'=>$now,'updated_at'=>$now);if('milestones'===$entity){$data['description']=sanitize_textarea_field((string)$r->get_param('description'));$data['status']='planned';$target=sanitize_text_field((string)$r->get_param('target_date'));if(''!==$target&&false===strtotime($target))return new WP_Error('projectpress_rest_date_invalid',__('The target date is invalid.','projectpress'),array('status'=>400));$data['target_date']=''===$target?null:gmdate('Y-m-d H:i:s',strtotime($target));}else{$data['content']=sanitize_textarea_field((string)$r->get_param('content'));$data['status']='open';}return $data;}
	private static function resolve_project(WP_REST_Request $r){$id=sanitize_text_field((string)$r->get_param('project'));if(''===$id)return new WP_Error('projectpress_rest_project_required',__('A project identifier is required.','projectpress'),array('status'=>400));$result=self::projects()->resolve($id);if(is_wp_error($result)&&!isset($result->error_data[$result->get_error_code()]))$result->add_data(array('status'=>404),$result->get_error_code());return $result;}
	private static function project_data(Project $p):array{return $p->to_array();}
	private static function success($data,int $status=200,array $meta=array()):WP_REST_Response{$body=array('success'=>true,'data'=>$data);if($meta)$body['meta']=$meta;return new WP_REST_Response($body,$status);}
	private static function domain_response($result,int $status=200){return is_wp_error($result)?$result:self::success($result instanceof Project?self::project_data($result):($result instanceof \ProjectPress\Includes\Project\Work\Task?$result->to_array():$result),$status);}
	private static function forbidden():WP_Error{return new WP_Error('projectpress_rest_forbidden',__('You do not have permission to perform this action.','projectpress'),array('status'=>rest_authorization_required_code()));}
	private static function not_found(string $message):WP_Error{return new WP_Error('projectpress_rest_not_found',$message,array('status'=>404));}
	private static function projects():ProjectManager{return self::$projects??=ProjectManager::get_instance();}
	private static function permissions():ProjectPermissions{return self::$permissions??=new ProjectPermissions();}
	public static function validate_licence(WP_REST_Request $r):WP_REST_Response|WP_Error{$token=sanitize_text_field((string)$r->get_param('token'));$product=sanitize_key((string)$r->get_param('product_id'));$site=esc_url_raw((string)$r->get_param('site_url'));if(''===$token||''===$product)return new WP_Error('invalid_request','A licence token and product id are required.',array('status'=>400));$valid=LicenceManager::validate_license($token,$product,''!==$site?$site:null);return new WP_REST_Response(array('valid'=>$valid,'token'=>$token,'product_id'=>$product),$valid?200:400);}
}
