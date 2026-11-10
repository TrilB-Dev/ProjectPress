<?php
/**
 * General tool settings for ProjectPress.
 *
 * @package ProjectPress\Admin\Manager\Tools
 */
namespace ProjectPress\Admin\Manager\Tools;

use ProjectPress\Assets\Assets;
use ProjectPress\Includes\Core\Schema;
use ProjectPress\Includes\Functions\Helpers\AlertHelper;
use ProjectPress\Includes\Functions\Helpers\DBHelper;
use ProjectPress\Includes\Functions\Helpers\FormFieldHelper;
use ProjectPress\Includes\Functions\Helpers\LoaderHelper;
use ProjectPress\Includes\Functions\Helpers\PermissionHelper;
use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class General extends ToolsManager {
	/**
	 * Register the general tools settings save action.
	 *
	 * @param LoaderHelper|null $loader Optional loader instance.
	 */
	public function __construct( ?LoaderHelper $loader = null ) {
		parent::__construct( false );
		( $loader ?? new LoaderHelper() )->register_component(
			$this,
			array(
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_projectpress_save_tools_general',
					'callback' => 'handle_save',
				),
				array(
					'type'     => 'action',
					'hook'     => 'admin_post_projectpress_reinstall_tables',
					'callback' => 'handle_reinstall_tables',
				),
			)
		)->run();
	}

	/**
	 * Render the general tools settings page.
	 *
	 * @return void
	 */
	public function render_page_content(): void {
		if ( '1' === RequestHelper::get_text( 'settings_saved' ) ) {
			AlertHelper::render_admin_notice( __( 'The general tools settings were saved.', 'projectpress' ), 'success' );
		}
		if ( '1' === RequestHelper::get_text( 'tables_reinstalled' ) ) {
			AlertHelper::render_admin_notice( __( 'The ProjectPress tables were reinstalled successfully.', 'projectpress' ), 'success' );
		}
		if ( '1' === RequestHelper::get_text( 'tables_reinstall_failed' ) ) {
			AlertHelper::render_admin_notice( __( 'The ProjectPress tables could not be reinstalled.', 'projectpress' ), 'error' );
		}

		$values = array(
			'remove_all_data_on_uninstall' => Settings::get_bool( 'remove_all_data_on_uninstall', false ),
			'uninstall_3rd_party_plugins'  => Settings::get_bool( 'uninstall_3rd_party_plugins', false ),
		);
		?>
		<div class="card shadow-sm mb-4">
			<div class="card-body">
				<h2 class="h5"><?php esc_html_e( 'General tools settings', 'projectpress' ); ?></h2>
				<p class="text-secondary"><?php esc_html_e( 'Configure the default cleanup behaviour used when ProjectPress is uninstalled or deactivated.', 'projectpress' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php echo FormFieldHelper::input( 'action', 'projectpress_save_tools_general', array( 'type' => 'hidden' ) ); ?>
					<?php wp_nonce_field( 'projectpress_tools_general', 'projectpress_tools_general_nonce' ); ?>
					<div class="mt-3">
						<?php echo FormFieldHelper::checkbox(
							'projectpress_tools[remove_all_data_on_uninstall]',
							'1',
							__( 'Remove all ProjectPress data on uninstall', 'projectpress' ),
							array(
								'id'      => 'projectpress-remove-all-data-on-uninstall',
								'checked' => ! empty( $values['remove_all_data_on_uninstall'] ),
							)
						); ?>
					</div>
					<div class="mt-3">
						<?php echo FormFieldHelper::checkbox(
							'projectpress_tools[uninstall_3rd_party_plugins]',
							'1',
							__( 'Remove 3rd party plugins on uninstall', 'projectpress' ),
							array(
								'id'      => 'projectpress-uninstall-3rd-party-plugins',
								'checked' => ! empty( $values['uninstall_3rd_party_plugins'] ),
							)
						); ?>
					</div>
					<div class="mt-4">
						<?php echo FormFieldHelper::button(
							__( 'Save settings', 'projectpress' ),
							array(
								'type'  => 'submit',
								'class' => 'btn-primary',
							)
						); ?>
					</div>
				</form>
			</div>
		</div>

		<div class="card shadow-sm">
			<div class="card-body">
				<h2 class="h5"><?php esc_html_e( 'Database repair', 'projectpress' ); ?></h2>
				<p class="text-secondary"><?php esc_html_e( 'Recreate the ProjectPress database tables from the registered schema when you need to repair a broken install.', 'projectpress' ); ?></p>
				<div class="d-flex flex-wrap gap-2">
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<?php echo FormFieldHelper::input( 'action', 'projectpress_reinstall_tables', array( 'type' => 'hidden' ) ); ?>
						<?php echo FormFieldHelper::input( 'mode', 'missing', array( 'type' => 'hidden' ) ); ?>
						<?php wp_nonce_field( 'projectpress_reinstall_tables', 'projectpress_reinstall_tables_nonce' ); ?>
						<?php echo FormFieldHelper::button(
							__( 'Create missing tables', 'projectpress' ),
							array(
								'type'  => 'submit',
								'class' => 'btn-secondary',
							)
						); ?>
					</form>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<?php echo FormFieldHelper::input( 'action', 'projectpress_reinstall_tables', array( 'type' => 'hidden' ) ); ?>
						<?php echo FormFieldHelper::input( 'mode', 'full', array( 'type' => 'hidden' ) ); ?>
						<?php wp_nonce_field( 'projectpress_reinstall_tables', 'projectpress_reinstall_tables_nonce' ); ?>
						<?php echo FormFieldHelper::button(
							__( 'Reinstall tables', 'projectpress' ),
							array(
								'type'  => 'submit',
								'class' => 'btn-danger',
							)
						); ?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save the general tools settings.
	 *
	 * @return void
	 */
	public function handle_save(): void {
		if ( ! PermissionHelper::can( 'projectpress_tools_general' ) ) {
			wp_die( esc_html__( 'You are not authorized to save these ProjectPress tools settings.', 'projectpress' ) );
		}

		check_admin_referer( 'projectpress_tools_general', 'projectpress_tools_general_nonce' );

		$input = isset( $_POST['projectpress_tools'] ) && is_array( $_POST['projectpress_tools'] ) ? wp_unslash( $_POST['projectpress_tools'] ) : array();
		$input = array(
			'remove_all_data_on_uninstall' => ! empty( $input['remove_all_data_on_uninstall'] ),
			'uninstall_3rd_party_plugins'  => ! empty( $input['uninstall_3rd_party_plugins'] ),
		);

		foreach ( $input as $key => $value ) {
			Settings::set( $key, $value );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=projectpress&group=tools&tab=general&settings_saved=1' ) );
		exit;
	}

	/**
	 * Recreate the registered ProjectPress database tables.
	 *
	 * @return void
	 */
	public function handle_reinstall_tables(): void {
		if ( ! PermissionHelper::can( 'projectpress_tools_general' ) ) {
			wp_die( esc_html__( 'You are not authorized to reinstall the ProjectPress tables.', 'projectpress' ) );
		}

		check_admin_referer( 'projectpress_reinstall_tables', 'projectpress_reinstall_tables_nonce' );

		$mode = RequestHelper::get_text( 'mode' );
		$mode = in_array( $mode, array( 'missing', 'full' ), true ) ? $mode : 'missing';

		$success = $this->reinstall_tables( 'full' === $mode );
		wp_safe_redirect( admin_url( 'admin.php?page=projectpress&group=tools&tab=general&' . ( $success ? 'tables_reinstalled=1' : 'tables_reinstall_failed=1' ) ) );
		exit;
	}

	/**
	 * Recreate the ProjectPress schema tables.
	 *
	 * @param bool $full_reinstall Whether to drop all ProjectPress tables before recreating them.
	 * @return bool
	 */
	private function reinstall_tables( bool $full_reinstall = false ): bool {
		Schema::register_tables();
		$tables = array();

		foreach ( array_merge( DBHelper::get_core_tables(), DBHelper::get_plugin_tables() ) as $table => $schema ) {
			if ( is_string( $table ) && '' !== trim( $table ) ) {
				$tables[] = $table;
			}
		}
		$tables = array_values( array_unique( $tables ) );

		if ( $full_reinstall ) {
			foreach ( $tables as $table ) {
				DBHelper::drop_table( $table );
			}
		}

		foreach ( $tables as $table ) {
			DBHelper::create_table( $table );
		}

		return ! empty( $tables );
	}

	/**
	 * Backward-compatible render signature used by the settings UI.
	 *
	 * @param array<string, mixed> $values Current settings values.
	 * @return void
	 */
	public function render( array $values = array() ): void {
		$values = array(
			'remove_all_data_on_uninstall' => ! empty( $values['remove_all_data_on_uninstall'] ) || Settings::get_bool( 'remove_all_data_on_uninstall', false ),
			'uninstall_3rd_party_plugins'  => ! empty( $values['uninstall_3rd_party_plugins'] ) || Settings::get_bool( 'uninstall_3rd_party_plugins', false ),
		);
		?>
		<tr>
			<th scope="row"><?php echo wp_kses_post( FormFieldHelper::label( 'projectpress-remove-all-data-on-uninstall', __( 'Remove all data on uninstall', 'projectpress' ) ) ); ?></th>
			<td>
				<?php echo wp_kses_post( FormFieldHelper::checkbox(
					'projectpress_tools[remove_all_data_on_uninstall]',
					'1',
					__( 'Remove all ProjectPress data when the plugin is uninstalled', 'projectpress' ),
					array(
						'id'      => 'projectpress-remove-all-data-on-uninstall',
						'checked' => ! empty( $values['remove_all_data_on_uninstall'] ),
					)
				) ); ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php echo wp_kses_post( FormFieldHelper::label( 'projectpress-uninstall-3rd-party-plugins', __( 'Third-party plugins on uninstall', 'projectpress' ) ) ); ?></th>
			<td>
				<?php echo wp_kses_post( FormFieldHelper::checkbox(
					'projectpress_tools[uninstall_3rd_party_plugins]',
					'1',
					__( 'Remove 3rd party plugins when ProjectPress is uninstalled', 'projectpress' ),
					array(
						'id'      => 'projectpress-uninstall-3rd-party-plugins',
						'checked' => ! empty( $values['uninstall_3rd_party_plugins'] ),
					)
				) ); ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Register page assets.
	 *
	 * @param Assets $assets Assets manager instance.
	 * @return void
	 */
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, array( 'projectpress' ), 'tools' );
	}
}



