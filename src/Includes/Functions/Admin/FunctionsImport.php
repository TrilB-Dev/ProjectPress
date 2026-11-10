<?php
/**
 * Import-related admin functions for ProjectPress.
 *
 * @package ProjectPress
 * @subpackage Includes\Functions\Admin
 * @since 1.0.0
 */
namespace ProjectPress\Includes\Functions\Admin;

use ProjectPress\Includes\Core\Defaults;
use ProjectPress\Includes\Tools\DataTransfer;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class FunctionsImport {

    /**
     * Import ProjectPress data from an uploaded JSON file.
     *
     * @return void
     */
    public function import_data(): void {
        if ( ! \current_user_can( 'projectpress_tools_import' ) ) {
            \wp_die( \esc_html__( 'You are not allowed to import ProjectPress data.', 'projectpress' ), 403 );
        }
        \check_admin_referer( 'projectpress_import' );
        $file = $_FILES['projectpress_import_file'] ?? array();
        if ( empty( $file['tmp_name'] ) || ( $file['error'] ?? UPLOAD_ERR_NO_FILE ) !== UPLOAD_ERR_OK ) {
            \wp_die( \esc_html__( 'Please upload a valid ProjectPress JSON export.', 'projectpress' ), 400 );
        }
        $data = json_decode( file_get_contents( $file['tmp_name'] ), true );
        if ( ! is_array( $data ) ) {
            \wp_die( \esc_html__( 'The uploaded file is not valid JSON.', 'projectpress' ), 400 );
        }
        $result = DataTransfer::import( $data );
        if ( is_wp_error( $result ) ) {
            \wp_die( \esc_html( $result->get_error_message() ), 400 );
        }
        \wp_safe_redirect( \admin_url( 'admin.php?page=projectpress&group=tools&tab=import&imported=1' ) );
        exit;
    }

    public function import_group_defaults(): void {
        if ( ! \current_user_can( 'projectpress_tools_import' ) ) {
            \wp_die( \esc_html__( 'You are not allowed to import the default ProjectPress groups.', 'projectpress' ), 403 );
        }

        \check_admin_referer( 'projectpress_import_group_defaults', 'projectpress_import_group_defaults_nonce' );

        if ( empty( $_POST['confirm_default_reset'] ) || '1' !== (string) $_POST['confirm_default_reset'] ) {
            \wp_safe_redirect( \admin_url( 'admin.php?page=projectpress&group=tools&tab=import&default_groups_import_failed=1' ) );
            exit;
        }

        $success = Defaults::reset_group_defaults();
        \wp_safe_redirect( \admin_url( 'admin.php?page=projectpress&group=tools&tab=import&default_groups_imported=' . ( $success ? '1' : '0' ) ) );
        exit;
    }
}

