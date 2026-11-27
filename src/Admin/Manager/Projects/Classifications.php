<?php
/**
 * Project classification administration.
 *
 * @package ProjectPress
 * @subpackage Admin\Manager\Projects
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\Projects;

use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\Core\ProjectClassificationRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Classifications {
	/**
	 * Taxonomy name.
	 *
	 * @var string
	 */
	protected string $taxonomy;

	/**
	 * Classification title.
	 *
	 * @var string
	 */
	protected string $title;

	/**
	 * Classification repository.
	 *
	 * @var ProjectClassificationRepository
	 */
	private ProjectClassificationRepository $repository;

	/**
	 * Constructor.
	 *
	 * @param string $taxonomy Taxonomy name.
	 * @param string $title    Classification title.
	 */
	public function __construct( string $taxonomy, string $title ) {
		$this->taxonomy   = $taxonomy;
		$this->title      = $title;
		$this->repository = new ProjectClassificationRepository();
	}

	/**
	 * Render the classification management interface.
	 *
	 * @return void
	 */
	public function render(): void {
		$terms = $this->repository->terms( $this->taxonomy );
		?>
		<div class="row g-4">
			<div class="col-lg-5">
				<div class="card card-body">
					<h2 class="h4">
						<?php
						/* translators: %s: Classification title. */
						echo esc_html( sprintf( __( 'Add %s', 'projectpress' ), $this->title ) );
						?>
					</h2>

					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="projectpress_classification_save" />
						<input type="hidden" name="taxonomy" value="<?php echo esc_attr( $this->taxonomy ); ?>" />
						<?php wp_nonce_field( 'projectpress_classification_save' ); ?>

						<div class="mb-3">
							<label class="form-label" for="classification-name">
								<?php esc_html_e( 'Name', 'projectpress' ); ?>
							</label>
							<input class="form-control" id="classification-name" name="name" required />
						</div>

						<div class="mb-3">
							<label class="form-label" for="classification-description">
								<?php esc_html_e( 'Description', 'projectpress' ); ?>
							</label>
							<textarea class="form-control" id="classification-description" name="description" rows="4"></textarea>
						</div>

						<div class="row g-3">
							<div class="col-6">
								<label class="form-label" for="classification-colour">
									<?php esc_html_e( 'Colour', 'projectpress' ); ?>
								</label>
								<input class="form-control form-control-color" id="classification-colour" name="colour" type="color" />
							</div>
							<div class="col-6">
								<label class="form-label" for="classification-icon">
									<?php esc_html_e( 'Icon key', 'projectpress' ); ?>
								</label>
								<input class="form-control" id="classification-icon" name="icon" />
							</div>
						</div>

						<div class="form-check mt-3">
							<input class="form-check-input" id="classification-enabled" name="enabled" type="checkbox" value="1" checked />
							<label class="form-check-label" for="classification-enabled">
								<?php esc_html_e( 'Enabled', 'projectpress' ); ?>
							</label>
						</div>

						<button class="btn btn-primary mt-3" type="submit">
							<?php esc_html_e( 'Add', 'projectpress' ); ?>
						</button>
					</form>
				</div>
			</div>

			<div class="col-lg-7">
				<div class="card card-body">
					<h2 class="h4"><?php echo esc_html( $this->title ); ?></h2>
					<div class="list-group list-group-flush">
						<?php if ( empty( $terms ) ) : ?>
							<p><?php esc_html_e( 'No items found.', 'projectpress' ); ?></p>
						<?php endif; ?>

						<?php foreach ( $terms as $term ) : ?>
							<div class="list-group-item d-flex justify-content-between">
								<span><?php echo esc_html( $term->name ); ?></span>
								<code><?php echo esc_html( $term->slug ); ?></code>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle classification creation.
	 *
	 * @return void
	 */
	public function handle_save(): void {
		check_admin_referer( 'projectpress_classification_save' );

		if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'projectpress_projects_taxonomies' ) ) {
			wp_die( esc_html__( 'You are not authorised to manage project classifications.', 'projectpress' ) );
		}

		$taxonomy = RequestHelper::key( $_POST, 'taxonomy', '' );
		$result   = $this->repository->create_term(
			$taxonomy,
			RequestHelper::text( $_POST, 'name', '' ),
			array(
				'description' => RequestHelper::text( $_POST, 'description', '' ),
				'colour'      => RequestHelper::text( $_POST, 'colour', '' ),
				'icon'        => RequestHelper::key( $_POST, 'icon', '' ),
				'enabled'     => RequestHelper::boolean( $_POST, 'enabled', false ),
			)
		);

		$tabs = array(
			'pp_project_type'     => 'types',
			'pp_project_category' => 'categories',
			'pp_project_tag'      => 'tags',
			'pp_project_label'    => 'labels',
		);
		$tab  = $tabs[ $taxonomy ] ?? 'projects';
		$url  = admin_url( 'admin.php?page=projectpress&group=project-manager&tab=' . $tab );

		if ( is_wp_error( $result ) ) {
			$url = add_query_arg( 'project_error', rawurlencode( $result->get_error_message() ), $url );
		}

		wp_safe_redirect( $url );
		exit;
	}
}
