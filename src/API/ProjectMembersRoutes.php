<?php
namespace ProjectPress\API;

use ProjectPress\Includes\Functions\Helpers\DBHelper;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\TeamRepository;
use ProjectPress\Includes\Project\Core\TeamService;
use ProjectPress\Includes\Project\ProjectManager;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class ProjectMembersRoutes {
	public static function register_routes(): void {
		register_rest_route( Routes::NAMESPACE, '/projects/(?P<project>[A-Za-z0-9_-]+)/members', array(
			array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( self::class, 'index' ), 'permission_callback' => array( self::class, 'can_view' ) ),
			array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( self::class, 'assign' ), 'permission_callback' => array( self::class, 'can_manage' ), 'args' => array( 'team_id' => array( 'required' => true, 'type' => 'integer', 'sanitize_callback' => 'absint' ), 'role' => array( 'type' => 'string', 'enum' => array( 'owner', 'manager', 'contributor', 'viewer' ), 'sanitize_callback' => 'sanitize_key' ) ) ),
		) );
		register_rest_route( Routes::NAMESPACE, '/projects/(?P<project>[A-Za-z0-9_-]+)/members/(?P<team_id>\d+)', array( 'methods' => WP_REST_Server::DELETABLE, 'callback' => array( self::class, 'remove' ), 'permission_callback' => array( self::class, 'can_manage' ) ) );
	}
	public static function index( WP_REST_Request $request ): WP_REST_Response {
		$project = self::project( $request ); $teams = new TeamRepository(); $data = array();
		foreach ( DBHelper::get_all( 'project_teams', array( 'project_id' => $project->get_id() ), '*', 'assigned_at ASC' ) as $assignment ) {
			$team = $teams->find( (int) $assignment['team_id'] ); if ( ! $team ) { continue; }
			$members = array(); foreach ( $teams->members( (int) $team['id'] ) as $member ) { $user = get_user_by( 'id', (int) $member['user_id'] ); if ( $user ) { $members[] = array( 'user_id' => (int) $member['user_id'], 'name' => $user->display_name, 'role' => $member['member_role'], 'status' => $member['member_status'] ); } }
			$data[] = array( 'team' => $team, 'project_role' => $assignment['team_role'], 'members' => $members );
		}
		return new WP_REST_Response( array( 'success' => true, 'data' => $data ), 200 );
	}
	public static function assign( WP_REST_Request $request ) { $project=self::project($request);$teams=new TeamRepository();$service=new TeamService($teams,ProjectManager::get_instance()->repository(),new ProjectPermissions());$result=$service->assign_project(absint($request['team_id']),$project->get_id(),sanitize_key((string)($request['role']?:'contributor')));return is_wp_error($result)?$result:new WP_REST_Response(array('success'=>true,'data'=>array('assigned'=>true)),201); }
	public static function remove( WP_REST_Request $request ) { $project=self::project($request);$teams=new TeamRepository();$service=new TeamService($teams,ProjectManager::get_instance()->repository(),new ProjectPermissions());$result=$service->remove_project(absint($request['team_id']),$project->get_id());return is_wp_error($result)?$result:new WP_REST_Response(array('success'=>true,'data'=>array('removed'=>true)),200); }
	public static function can_view( WP_REST_Request $request ) { $project=self::resolve($request);return is_wp_error($project)?$project:((new ProjectPermissions())->can_view($project)?true:self::forbidden()); }
	public static function can_manage( WP_REST_Request $request ) { $project=self::resolve($request);return is_wp_error($project)?$project:((new ProjectPermissions())->can_edit($project)?true:self::forbidden()); }
	private static function project(WP_REST_Request $request){return ProjectManager::get_instance()->resolve(sanitize_text_field((string)$request['project']));}
	private static function resolve(WP_REST_Request $request){$project=self::project($request);if(is_wp_error($project)){$project->add_data(array('status'=>404),$project->get_error_code());}return $project;}
	private static function forbidden():WP_Error{return new WP_Error('projectpress_rest_forbidden',__('You do not have permission to perform this action.','projectpress'),array('status'=>rest_authorization_required_code()));}
}
