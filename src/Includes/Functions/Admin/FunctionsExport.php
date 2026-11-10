<?php
/**
 * Export-related admin functions for ProjectPress.
 *
 * @package ProjectPress
 * @subpackage Includes\Functions\Admin
 * @since 1.0.0
 */
namespace ProjectPress\Includes\Functions\Admin;

use ProjectPress\Includes\Tools\DataTransfer;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class FunctionsExport {

    /**
     * Export ProjectPress data as a JSON file.
     *
     * @return void
     */
    public function export_data(): void {
        if ( ! current_user_can( 'projectpress_tools_export' ) ) {
            wp_die( esc_html__( 'You are not allowed to export ProjectPress data.', 'projectpress' ), 403 );
        }
        check_admin_referer( 'projectpress_export' );
        nocache_headers();
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=projectpress-export-' . gmdate( 'Y-m-d' ) . '.json' );
        echo wp_json_encode( DataTransfer::export(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
        exit;
    }
}

