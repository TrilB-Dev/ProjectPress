<?php
/**
 * Import class for ProjectPress plugin.
 *
 * @package ProjectPress
 * @subpackage Admin\Manager\Tools
 * @since 1.0.0
 */
namespace ProjectPress\Admin\Manager\Tools;

use ProjectPress\Includes\Functions\Admin\FunctionsImport;
use ProjectPress\Includes\Functions\Helpers\AlertHelper;
use ProjectPress\Includes\Functions\Helpers\FormFieldHelper;
use ProjectPress\Includes\Functions\Helpers\LoaderHelper;
use ProjectPress\Includes\Functions\Helpers\UrlHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Import extends ToolsManager {
	/**
	 * Import handler instance.
	 *
	 * @var FunctionsImport
	 */
	private FunctionsImport $import_functions;

	/**
	 * Render the JSON import form below the tools settings form.
	 *
	 * @return void
	 */
	public function __construct( ?LoaderHelper $loader = null ) {
		parent::__construct( false );
		$this->import_functions = new FunctionsImport();
		( $loader ?? new LoaderHelper() )->register_component(
			$this->import_functions,
			array(
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_projectpress_import',
					'callback' => 'import_data',
				),
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_projectpress_import_group_defaults',
					'callback' => 'import_group_defaults',
				),
			)
		)->run();
	}
	/**
	 * Render the import page content using the shared manager contract.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		$this->render();
	}

	/**
	 * Render the import form.
	 *
	 * @return void
	 */
	public function render(): void {
		if ( '1' === (string) ( $_GET['default_groups_imported'] ?? '' ) ) {
			AlertHelper::render_admin_notice( \__( 'The default ProjectPress groups and taxonomy terms were imported successfully.', 'projectpress' ), 'success' );
		}
		if ( '1' === (string) ( $_GET['default_groups_import_failed'] ?? '' ) ) {
			AlertHelper::render_admin_notice( \__( 'The default ProjectPress groups could not be imported.', 'projectpress' ), 'error' );
		}
		if ( '1' === (string) ( $_GET['default_file_types_imported'] ?? '' ) ) {
			AlertHelper::render_admin_notice( \__( 'The default ProjectPress file types were imported successfully.', 'projectpress' ), 'success' );
		}
		if ( '1' === (string) ( $_GET['default_mod_statuses_imported'] ?? '' ) ) {
			AlertHelper::render_admin_notice( \__( 'The default ProjectPress mod statuses were imported successfully.', 'projectpress' ), 'success' );
		}
		?>
		<form
			method="post"
			action="<?php echo \esc_url( UrlHelper::admin_action( 'projectpress_import' ) ); ?>"
			enctype="multipart/form-data"
			class="card projectpress-import-form shadow-sm mt-4"
		>
			<?php echo \wp_kses_post( FormFieldHelper::input( 'action', 'projectpress_import', array( 'type' => 'hidden' ) ) ); ?>
			<?php \wp_nonce_field( 'projectpress_import' ); ?>
			<div class="card-body">
				<?php
				echo \wp_kses_post(
					FormFieldHelper::label(
						'projectpress-import-file',
						\__( 'Import ProjectPress JSON', 'projectpress' ),
						array(
							'description'  => \__( 'Select a JSON export containing ProjectPress records, customer data, and validation metadata.', 'projectpress' ),
							'tooltip'      => \__( 'Import should use a valid archive and, where required, a password-protected file to preserve security.', 'projectpress' ),
							'tooltip_icon' => 'fa-file-import',
						)
					)
				);
				echo \wp_kses_post(
					FormFieldHelper::input(
						'projectpress_import_file',
						'',
						array(
							'id'       => 'projectpress-import-file',
							'type'     => 'file',
							'class'    => 'mb-3',
							'accept'   => 'application/json,.json',
							'required' => true,
						)
					)
				);
				echo \wp_kses_post(
					FormFieldHelper::button(
						\__( 'Import ProjectPress JSON', 'projectpress' ),
						array(
							'type'  => 'submit',
							'class' => 'btn-primary',
						)
					)
				);
				?>
			</div>
		</form>
		<div class="card projectpress-import-form shadow-sm mt-4">
			<form
				method="post"
				action="<?php echo \esc_url( UrlHelper::admin_action( 'projectpress_import_group_defaults' ) ); ?>"
			>
				<?php \wp_nonce_field( 'projectpress_import_group_defaults', 'projectpress_import_group_defaults_nonce' ); ?>
				<input type="hidden" name="action" value="projectpress_import_group_defaults" />
				<input type="hidden" name="import_target" value="projectpress_file_type" />
				<div class="card-body">
					<h3 class="h5 mb-3"><?php \esc_html_e( 'Import File Type Defaults', 'projectpress' ); ?></h3>
					<p class="text-secondary mb-3"><?php \esc_html_e( 'This will clear the existing default file types and rebuild the default file types table from scratch.', 'projectpress' ); ?></p>
					<?php echo \wp_kses_post(
						FormFieldHelper::button(
							\__( 'Import File Types', 'projectpress' ),
							array(
								'type'  => 'submit',
								'class' => 'btn-warning',
							)
						)
					); ?>
				</div>
			</form>
		</div>
		<div class="card projectpress-import-form shadow-sm mt-4">
			<form
				method="post"
				action="<?php echo \esc_url( UrlHelper::admin_action( 'projectpress_import_group_defaults' ) ); ?>"
			>
				<?php \wp_nonce_field( 'projectpress_import_group_defaults', 'projectpress_import_group_defaults_nonce' ); ?>
				<input type="hidden" name="action" value="projectpress_import_group_defaults" />
				<input type="hidden" name="import_target" value="projectpress_mod_status" />
				<div class="card-body">
					<h3 class="h5 mb-3"><?php \esc_html_e( 'Import Status Defaults', 'projectpress' ); ?></h3>
					<p class="text-secondary mb-3"><?php \esc_html_e( 'This will clear the existing default status terms and rebuild the default statuses table from scratch.', 'projectpress' ); ?></p>
					<?php echo \wp_kses_post(
						FormFieldHelper::button(
							\__( 'Import Statuses', 'projectpress' ),
							array(
								'type'  => 'submit',
								'class' => 'btn-warning',
							)
						)
					); ?>
				</div>
			</form>
		</div> <!-- Close card for Import Statuses -->

		<div class="card projectpress-import-form shadow-sm mt-4">
			<div class="card-body">
				<h3 class="h5 mb-3"><?php \esc_html_e( 'Import Group Defaults', 'projectpress' ); ?></h3>
				<p class="text-secondary mb-3"><?php \esc_html_e( 'This will clear the existing default taxonomy terms and rebuild the default groups table from scratch.', 'projectpress' ); ?></p>
				<button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#projectpress-import-default-groups-modal">
					<?php \esc_html_e( 'Import Group Defaults', 'projectpress' ); ?>
				</button>
			</div>
		</div>

		<div class="modal fade" id="projectpress-import-default-groups-modal" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<form method="post" action="<?php echo \esc_url( UrlHelper::admin_action( 'projectpress_import_group_defaults' ) ); ?>">
					<?php \wp_nonce_field( 'projectpress_import_group_defaults', 'projectpress_import_group_defaults_nonce' ); ?>
					<input type="hidden" name="action" value="projectpress_import_group_defaults" />
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title"><?php \esc_html_e( 'Import Group Defaults', 'projectpress' ); ?></h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php \esc_attr_e( 'Close', 'projectpress' ); ?>"></button>
						</div>
						<div class="modal-body">
							<p><?php \esc_html_e( 'By importing the group defaults, all ProjectPress data entered by the user will be deleted and cannot be undone.', 'projectpress' ); ?></p>
							<?php echo FormFieldHelper::checkbox(
								'confirm_default_reset',
								'1',
								\__( 'I understand this will delete all user-entered data and cannot be undone.', 'projectpress' ),
								array(
									'id' => 'projectpress-confirm-default-reset',
								)
							); ?>
						</div>
						<div class="modal-footer d-flex justify-content-between">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php \esc_html_e( 'Cancel', 'projectpress' ); ?></button>
							<button type="submit" class="btn btn-danger"><?php \esc_html_e( 'Import', 'projectpress' ); ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<?php
	}
}




