<?php
/**
 * Debug class for ProjectPress plugin.
 *
 * @package ProjectPress
 * @subpackage Admin\Manager\Tools
 * @since 1.0.0
 */
namespace ProjectPress\Admin\Manager\Tools;

use ProjectPress\Includes\Functions\Helpers\FormFieldHelper;
use ProjectPress\Includes\MSGraph\GraphService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Debug extends ToolsManager {
	/**
	 * Constructor for the Debug class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct( false );
	}
	/**
	 * Render the debug settings page content.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		$diagnostics = null;
		if ( isset( $_POST['projectpress_run_graph_diagnostics'] ) ) {
			if ( ! current_user_can( 'projectpress_tools_debug' ) || ! check_admin_referer( 'projectpress_run_graph_diagnostics', 'projectpress_graph_diagnostics_nonce' ) ) {
				wp_die( esc_html__( 'You are not authorized to run Microsoft Graph diagnostics.', 'projectpress' ) );
			}

			try {
				$diagnostics_service    = $graph->get_diagnostics();
				$curl_diagnostics       = $diagnostics_service->test_direct_curl_connection();
				$http_graph_diagnostics = $diagnostics_service->test_http_graph_connection();
				$diagnostics            = array(
					'success'    => ! empty( $curl_diagnostics['success'] ) && ! empty( $http_graph_diagnostics['success'] ),
					'message'    => __( 'Microsoft Graph diagnostics completed.', 'projectpress' ),
					'curl'       => $curl_diagnostics,
					'http_graph' => $http_graph_diagnostics,
				);
				$graph_error            = $graph->get_connection_error();
				if ( empty( $graph_error ) ) {
					$graph_error = 'none';
				}
				$diagnostics['diagnostics'] = array_merge(
					is_array( $curl_diagnostics['diagnostics'] ?? null ) ? $curl_diagnostics['diagnostics'] : array(),
					is_array( $http_graph_diagnostics['diagnostics'] ?? null ) ? $http_graph_diagnostics['diagnostics'] : array(),
					array(
						'graph_initialization_error' => $graph_error,
						'token_endpoint_probe'       => $diagnostics_service->probe_token_endpoint( (string) $graph->get_tenant_id() ),
						'dns_context'                => $diagnostics_service->get_dns_context_summary( array( 'login.microsoftonline.com', 'graph.microsoft.com' ) ),
						'proxy_context'              => $diagnostics_service->get_proxy_context_summary(),
						'http_hook_context'          => $diagnostics_service->get_http_hook_context_summary(),
					)
				);
			} catch ( \Throwable $error ) {
				$diagnostics = array(
					'success' => false,
					'message' => __( 'The diagnostics could not be completed.', 'projectpress' ),
					'trace'   => array( $error->getMessage() ),
				);
			}
		}
		?>
		<div class="card shadow-sm">
			<div class="card-body">
		<?php if ( is_array( $diagnostics ) ) : ?>
			<div class="notice <?php echo ! empty( $diagnostics['success'] ) ? 'notice-success' : 'notice-error'; ?> is-dismissible">
				<p><strong><?php echo esc_html( $diagnostics['message'] ?? __( 'Diagnostics completed.', 'projectpress' ) ); ?></strong></p>
			</div>
			<div class="row g-4 mb-4">
				<?php
				foreach ( array(
					'curl'       => __( 'cURL diagnostic', 'projectpress' ),
					'http_graph' => __( 'HTTP Graph diagnostic', 'projectpress' ),
				) as $transport => $title ) :
					?>
								<?php $result = is_array( $diagnostics[ $transport ] ?? null ) ? $diagnostics[ $transport ] : array(); ?>
					<div class="col-12 col-xl-6">
						<div class="card shadow-sm h-100">
							<div class="card-body">
								<h2 class="h5"><?php echo esc_html( $title ); ?></h2>
								<p class="mb-3"><strong><?php echo esc_html( $result['message'] ?? __( 'No result available.', 'projectpress' ) ); ?></strong></p>
								<?php if ( ! empty( $result['trace'] ) && is_array( $result['trace'] ) ) : ?>
									<pre class="bg-light border rounded p-3 mb-0" style="white-space: pre-wrap;"><?php echo esc_html( implode( "\n", array_map( 'strval', $result['trace'] ) ) ); ?></pre>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( ! empty( $diagnostics['diagnostics'] ) && is_array( $diagnostics['diagnostics'] ) ) : ?>
				<div class="card shadow-sm mb-4">
					<div class="card-body">
						<h2 class="h5"><?php esc_html_e( 'Shared diagnostic context', 'projectpress' ); ?></h2>
						<dl class="row mb-0">
							<?php foreach ( $diagnostics['diagnostics'] as $key => $value ) : ?>
								<dt class="col-sm-4 text-break"><?php echo esc_html( (string) $key ); ?></dt>
								<dd class="col-sm-8 text-break"><?php echo esc_html( is_array( $value ) ? wp_json_encode( $value ) : (string) $value ); ?></dd>
							<?php endforeach; ?>
						</dl>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<div class="card shadow-sm mb-4">
			<div class="card-body">
				<h2 class="h5"><?php esc_html_e( 'Microsoft Graph diagnostics', 'projectpress' ); ?></h2>
				<p class="text-secondary"><?php esc_html_e( 'Test the configured application credentials against Microsoft Entra over TLS 1.2. The test does not display credentials or tokens.', 'projectpress' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=projectpress&group=tools&tab=debug' ) ); ?>">
					<?php wp_nonce_field( 'projectpress_run_graph_diagnostics', 'projectpress_graph_diagnostics_nonce' ); ?>
					<?php echo FormFieldHelper::input( 'projectpress_run_graph_diagnostics', '1', array( 'type' => 'hidden' ) ); ?>
					<?php
					echo FormFieldHelper::button(
						__( 'Run Graph diagnostics', 'projectpress' ),
						array(
							'type'  => 'submit',
							'class' => 'btn-primary',
						)
					);
					?>
				</form>
				<p class="small text-secondary mt-3 mb-0"><?php esc_html_e( 'A successful result confirms token acquisition, but does not test delegated OAuth mailbox access.', 'projectpress' ); ?></p>
			</div>
		</div>
		<div class="card shadow-sm">
			<div class="card-body">
				<h2 class="h5"><?php esc_html_e( 'Debug logging', 'projectpress' ); ?></h2>
				<p class="text-secondary"><?php esc_html_e( 'Configure diagnostic logging from the Tools settings tab.', 'projectpress' ); ?></p>
				<a class="btn btn-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=projectpress&group=tools&tab=general' ) ); ?>"><?php esc_html_e( 'Open Tools Settings', 'projectpress' ); ?></a>
			</div>
		</div>
		</div>
		</div>
		<?php
	}

	/**
	 * Render debug-related settings fields.
	 *
	 * Intentionally accepts an optional values array to stay compatible with the
	 * parent ToolsManager contract while the debug tool renders its own page.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 */
	public function render( array $values = array() ): void {
		$field_id = 'projectpress-debug-logging';
		$field    = array(
			'description'  => __( 'Write diagnostic information to the WordPress debug log.', 'projectpress' ),
			'tooltip'      => __( 'Enable this only while investigating a problem, because logs can grow over time.', 'projectpress' ),
			'tooltip_type' => 'info',
		);
		?>
		<tr>
			<th scope="row"><?php echo wp_kses_post( FormFieldHelper::label( $field_id, __( 'Debug logging', 'projectpress' ), $field ) ); ?></th>
			<td><?php echo wp_kses_post(
				FormFieldHelper::checkbox(
					'projectpress_tools[debug_logging]',
					'1',
					__( 'Enable ProjectPress debug logging', 'projectpress' ),
					array(
						'id'      => $field_id,
						'checked' => ! empty( $values['debug_logging'] ),
					)
				)
			); ?></td>
		</tr>
		<?php
		$field_id = 'projectpress-console-logging';
		$field    = array(
			'description' => __( 'Write diagnostic information to the browser console.', 'projectpress' ),
			'tooltip'     => __( 'Use this during frontend troubleshooting and disable it afterward.', 'projectpress' ),
		);
		?>
		<tr>
			<th scope="row"><?php echo wp_kses_post( FormFieldHelper::label( $field_id, __( 'Console logging', 'projectpress' ), $field ) ); ?></th>
			<td><?php echo wp_kses_post(
				FormFieldHelper::checkbox(
					'projectpress_tools[console_logging]',
					'1',
					__( 'Enable browser console logging', 'projectpress' ),
					array(
						'id'      => $field_id,
						'checked' => ! empty( $values['console_logging'] ),
					)
				)
			); ?></td>
		</tr>
		<?php
	}
}




