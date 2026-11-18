<?php
namespace ProjectPress\Includes\Project\Core;

use ProjectPress\Includes\Functions\Helpers\PermissionHelper;

if ( ! defined( 'ABSPATH' ) ) { exit; }

final class ProjectPermissions {
	public function can_view( Project $project, ?int $user_id = null ): bool {
		$user_id = $user_id ?? PermissionHelper::user_id();
		$allowed = 'public' === $project->get_visibility() || $user_id === $project->get_owner_id() || user_can( $user_id, 'projectpress_projects_view' );
		return (bool) apply_filters( 'projectpress_user_can_view_project', $allowed, $user_id, $project );
	}

	public function can_edit( Project $project, ?int $user_id = null ): bool {
		$user_id = $user_id ?? PermissionHelper::user_id();
		$allowed = $user_id > 0 && ( $user_id === $project->get_owner_id() || user_can( $user_id, 'projectpress_projects_edit' ) );
		return (bool) apply_filters( 'projectpress_user_can_edit_project', $allowed, $user_id, $project );
	}

	public function can_create(): bool { return PermissionHelper::can( 'projectpress_projects_create' ); }
	public function can_delete( Project $project ): bool { return PermissionHelper::can( 'projectpress_projects_delete' ) || PermissionHelper::user_id() === $project->get_owner_id(); }
}
