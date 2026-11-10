<?php
/**
 * Export class for ProjectPress plugin.
 *
 * @package ProjectPress
 * @subpackage Admin\Manager\Tools
 * @since 1.0.0
 */
namespace ProjectPress\Admin\Manager\Tools;

use ProjectPress\Includes\Functions\Admin\FunctionsExport;
use ProjectPress\Includes\Functions\Helpers\FormFieldHelper;
use ProjectPress\Includes\Functions\Helpers\LoaderHelper;
use ProjectPress\Includes\Functions\Helpers\UrlHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Export extends ToolsManager {
	/**
	 * Export handler instance.
	 *
	 * @var FunctionsExport
	 */
	private FunctionsExport $export_functions;

	/**
	 * Constructor for the Export class.
	 *
	 * @return void
	 */
	public function __construct( ?LoaderHelper $loader = null ) {
		parent::__construct( false );
		$this->export_functions = new FunctionsExport();
		( $loader ?? new LoaderHelper() )->register_component(
			$this->export_functions,
			array(
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_projectpress_export',
					'callback' => 'export_data',
				),
			)
		)->run();
	}
	/**
	 * Render the export form.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		?>
		<div class="card shadow-sm">
			<div class="card-body">
				<h2 class="h5"><?php esc_html_e( 'Export ProjectPress data', 'projectpress' ); ?></h2>
				<p class="text-secondary"><?php esc_html_e( 'Download a protected JSON export of ProjectPress records, customer meta, and validation data.', 'projectpress' ); ?></p>
				<?php echo wp_kses_post(
					FormFieldHelper::button(
						esc_html__( 'Export ProjectPress JSON', 'projectpress' ),
						array(
							'href'  => UrlHelper::admin_action_nonce( 'projectpress_export', 'projectpress_export' ),
							'class' => 'btn-outline-primary',
						)
					)
				); ?>
			</div>
		</div>
		<?php
	}
	/**
	 * Render the export page content.
	 *
	 * @return void
	 */
	public function render(): void {
		?>
		<tr>
			<th scope="row"><?php echo wp_kses_post(
				FormFieldHelper::label(
					'projectpress-export',
					esc_html__( 'Import and export', 'projectpress' ),
					array(
						'description' => __( 'Export or import ProjectPress data as a password-protected JSON archive.', 'projectpress' ),
						'tooltip'     => __( 'Exports are protected with a WordPress nonce and should use a password whenever shared with partners.', 'projectpress' ),
					)
				)
			); ?></th>
			<td><?php echo wp_kses_post(
				FormFieldHelper::button(
					esc_html__( 'Export ProjectPress JSON', 'projectpress' ),
					array(
						'href'  => UrlHelper::admin_action_nonce( 'projectpress_export', 'projectpress_export' ),
						'class' => 'btn-outline-primary',
					)
				)
			); ?></td>
		</tr>
		<tr>
			<th scope="row"><?php echo FormFieldHelper::label(
				'projectpress-database-manager',
				esc_html__( 'Database manager', 'projectpress' ),
				array(
					'description' => __( 'ProjectPress records are kept in the plugin database tables and managed through the core lifecycle.', 'projectpress' ),
					'tooltip'     => __( 'Manual database changes are not required for normal ProjectPress operations.', 'projectpress' ),
				)
			); ?></th>
			<td><?php esc_html_e( 'Managed automatically', 'projectpress' ); ?></td>
		</tr>
		<?php
	}
}




