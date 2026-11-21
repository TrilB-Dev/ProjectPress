<?php
/**
 * REST API routes for ProjectPress.
 *
 * @package ProjectPress
 */
namespace ProjectPress\API;

use ProjectPress\Includes\Licence\LicenceManager;
use ProjectPress\Includes\Project\Core\Project;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\ProjectManager;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Routes {
	public const NAMESPACE = 'projectpress/v1';

	private static ?ProjectManager $projects = null;
	private static ?ProjectPermissions $permissions = null;

	/** Register ProjectPress REST routes. */
	public static function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/licence/validate',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( self::class, 'validate_licence' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/projects',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( self::class, 'projects_index' ),
					'permission_callback' => array( self::class, 'can_list_projects' ),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/projects/(?P<project>[A-Za-z0-9_-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( self::class, 'project_read' ),
					'permission_callback' => array( self::class, 'can_read_project' ),
					'args'                => array(
						'project' => array(
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			)
		);
	}

	/** Read the project collection using the existing domain query defaults. */
	public static function projects_index(): WP_REST_Response {
		$projects = array_map( array( self::class, 'project_data' ), self::projects()->query() );
		return self::success( $projects );
	}

	/** Read one project resolved by id, key or slug. */
	public static function project_read( WP_REST_Request $request ) {
		$project = self::resolve_project( $request );
		if ( is_wp_error( $project ) ) {
			return $project;
		}
		return self::success( self::project_data( $project ) );
	}

	/** Collection permission callback. */
	public static function can_list_projects() {
		if ( current_user_can( 'manage_options' ) || current_user_can( 'projectpress_projects_view' ) ) {
			return true;
		}
		return new WP_Error( 'projectpress_rest_forbidden', __( 'You do not have permission to view projects.', 'projectpress' ), array( 'status' => rest_authorization_required_code() ) );
	}

	/** Project permission callback. ProjectContext performs the project-specific view check. */
	public static function can_read_project( WP_REST_Request $request ) {
		$project = self::resolve_project( $request );
		return is_wp_error( $project ) ? $project : true;
	}

	/** Consistent successful response envelope for ProjectPress domain routes. */
	private static function success( $data, int $status = 200 ): WP_REST_Response {
		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => $data,
			),
			$status
		);
	}

	private static function resolve_project( WP_REST_Request $request ) {
		$identifier = sanitize_text_field( (string) $request->get_param( 'project' ) );
		if ( '' === $identifier ) {
			return new WP_Error( 'projectpress_rest_project_required', __( 'A project identifier is required.', 'projectpress' ), array( 'status' => 400 ) );
		}
		return self::projects()->resolve( $identifier );
	}

	private static function project_data( Project $project ): array {
		return $project->to_array();
	}

	private static function projects(): ProjectManager {
		return self::$projects ??= ProjectManager::get_instance();
	}

	private static function permissions(): ProjectPermissions {
		return self::$permissions ??= new ProjectPermissions();
	}

	/** Validate a licence for the plugin. */
	public static function validate_licence( WP_REST_Request $request ): WP_REST_Response|WP_Error {
		$token      = sanitize_text_field( (string) $request->get_param( 'token' ) );
		$product_id = sanitize_key( (string) $request->get_param( 'product_id' ) );
		$site_url   = esc_url_raw( (string) $request->get_param( 'site_url' ) );
		if ( '' === $token || '' === $product_id ) {
			return new WP_Error( 'invalid_request', 'A licence token and product id are required.' );
		}
		$valid = LicenceManager::validate_license( $token, $product_id, '' !== $site_url ? $site_url : null );
		return new WP_REST_Response( array( 'valid' => $valid, 'token' => $token, 'product_id' => $product_id ), $valid ? 200 : 400 );
	}
}
