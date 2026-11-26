<?php
/**
 * Team administration for the Global Project Manager.
 *
 * @package ProjectPress
 * @subpackage Admin\Manager\ProjectManager
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\ProjectManager;

use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\ProjectRepository;
use ProjectPress\Includes\Project\Core\TeamRepository;
use ProjectPress\Includes\Project\Core\TeamService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Teams {
	/**
	 * Team repository.
	 *
	 * @var TeamRepository
	 */
	private TeamRepository $repository;

	/**
	 * Team service.
	 *
	 * @var TeamService
	 */
	private TeamService $service;

	public function __construct() {
		$this->repository = new TeamRepository();
		$this->service    = new TeamService(
			$this->repository,
			new ProjectRepository(),
			new ProjectPermissions()
		);
	}

	public function render(): void {
		$teams = $this->repository->all();
		?>
		<div class="row g-4">
			<div class="col-lg-4">
				<div class="card card-body">
					<h2 class="h4"><?php esc_html_e( 'Create Team', 'projectpress' ); ?></h2>

					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="projectpress_team_save" />
						<?php wp_nonce_field( 'projectpress_team_save' ); ?>

						<label class="form-label" for="team-name">
							<?php esc_html_e( 'Name', 'projectpress' ); ?>
						</label>
						<input class="form-control" id="team-name" name="name" required />

						<label class="form-label mt-3" for="team-description">
							<?php esc_html_e( 'Description', 'projectpress' ); ?>
						</label>
						<textarea class="form-control" id="team-description" name="description"></textarea>

						<button class="btn btn-primary mt-3" type="submit">
							<?php esc_html_e( 'Create Team', 'projectpress' ); ?>
						</button>
					</form>
				</div>
			</div>

			<div class="col-lg-8">
				<div class="card card-body">
					<h2 class="h4"><?php esc_html_e( 'Teams', 'projectpress' ); ?></h2>

					<?php if ( empty( $teams ) ) : ?>
						<p><?php esc_html_e( 'No teams found.', 'projectpress' ); ?></p>
					<?php endif; ?>

					<?php foreach ( $teams as $team ) : ?>
						<?php $team_id = (int) $team['id']; ?>
						<div class="border-bottom py-3">
							<strong><?php echo esc_html( $team['team_name'] ); ?></strong>
							<div class="text-secondary mb-3">
								<?php echo esc_html( $team['team_description'] ); ?>
							</div>

							<div class="row g-3">
								<div class="col-md-6">
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
										<input type="hidden" name="action" value="projectpress_team_member_save" />
										<input type="hidden" name="team_id" value="<?php echo esc_attr( (string) $team_id ); ?>" />
										<?php wp_nonce_field( 'projectpress_team_member_save' ); ?>

										<label class="form-label"><?php esc_html_e( 'Add member by User ID', 'projectpress' ); ?></label>
										<input class="form-control" type="number" min="1" name="user_id" required />

										<select class="form-select mt-2" name="member_role">
											<option value="member">Member</option>
											<option value="manager">Manager</option>
											<option value="viewer">Viewer</option>
										</select>

										<button class="btn btn-outline-primary mt-2" type="submit">
											<?php esc_html_e( 'Add Member', 'projectpress' ); ?>
										</button>
									</form>
								</div>

								<div class="col-md-6">
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
										<input type="hidden" name="action" value="projectpress_team_project_save" />
										<input type="hidden" name="team_id" value="<?php echo esc_attr( (string) $team_id ); ?>" />
										<?php wp_nonce_field( 'projectpress_team_project_save' ); ?>

										<label class="form-label"><?php esc_html_e( 'Assign Project ID', 'projectpress' ); ?></label>
										<input class="form-control" type="number" min="1" name="project_id" required />

										<select class="form-select mt-2" name="team_role">
											<option value="contributor">Contributor</option>
											<option value="manager">Manager</option>
											<option value="viewer">Viewer</option>
											<option value="owner">Owner</option>
										</select>

										<button class="btn btn-outline-primary mt-2" type="submit">
											<?php esc_html_e( 'Assign Project', 'projectpress' ); ?>
										</button>
									</form>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function handle_save(): void {
		check_admin_referer( 'projectpress_team_save' );
		$result = $this->service->create(
			array(
				'name'        => RequestHelper::text( $_POST, 'name', '' ),
				'description' => RequestHelper::text( $_POST, 'description', '' ),
			)
		);
		$this->redirect( $result );
	}

	public function handle_member_save(): void {
		check_admin_referer( 'projectpress_team_member_save' );
		$result = $this->service->add_member(
			RequestHelper::integer( $_POST, 'team_id', 0 ),
			RequestHelper::integer( $_POST, 'user_id', 0 ),
			RequestHelper::key( $_POST, 'member_role', 'member' )
		);
		$this->redirect( $result );
	}

	public function handle_project_save(): void {
		check_admin_referer( 'projectpress_team_project_save' );
		$result = $this->service->assign_project(
			RequestHelper::integer( $_POST, 'team_id', 0 ),
			RequestHelper::integer( $_POST, 'project_id', 0 ),
			RequestHelper::key( $_POST, 'team_role', 'contributor' )
		);
		$this->redirect( $result );
	}

	private function redirect( $result ): void {
		$url = admin_url( 'admin.php?page=projectpress&group=project-manager&tab=teams' );
		$url = is_wp_error( $result )
			? add_query_arg( 'team_error', rawurlencode( $result->get_error_message() ), $url )
			: add_query_arg( 'team_saved', '1', $url );

		wp_safe_redirect( $url );
		exit;
	}
}
