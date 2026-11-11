<?php
/**
 * Assets class for GitHub plugin.
 *
 * @package ProjectPress\Includes\Plugins\GitHub\Assets
 */
namespace ProjectPress\Includes\Plugins\GitHub\Assets;

use ProjectPress\Assets\Assets as RootAssets;
use ProjectPress\Includes\Functions\Helpers\LoaderHelper;


final class Assets extends RootAssets {
    /**
     * Registers the asset filters for the GitHub plugin.
     *
     * @since 1.0.0
     */
    public function register(): void {
        ( new LoaderHelper() )->register_component(
            $this,
            array(
                array(
                    'type' => 'filter',
                    'hook' => 'projectpress_admin_assets',
                    'callback' => 'register_admin_assets',
                    'accepted_args' => 2,
                ),
                array(
                    'type' => 'filter',
                    'hook' => 'projectpress_frontend_assets',
                    'callback' => 'register_frontend_assets',
                    'accepted_args' => 2,
                ),
            )
        )->run();
        add_filter('script_loader_tag', array( $this, 'add_crossorigin_attribute' ), 10, 3);
    }

    /**
     * Register GitHub asset definitions for the admin context.
     *
     * @param array  $assets The existing asset bundle.
     * @param string $context The current asset context.
     * @return array The updated asset bundle.
     */
    public function register_admin_assets( array $assets, string $context = 'admin' ): array {
        if ( 'admin' !== $context ) {
            return $assets;
        }

        $current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
        $current_group = isset( $_GET['group'] ) ? sanitize_text_field( wp_unslash( $_GET['group'] ) ) : '';
        $current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';

        if ( 'projectpress' === $current_page && 'settings' === $current_group && 'plugins' === $current_tab ) {
            $assets['scripts'][] = array(
                'handle' => 'projectpress-github-plugin-settings',
                'src' => PROJECTPRESS_PLUGINS_URL . '/GitHub/Assets/dist/js/admin.plugins.github.js',
                'deps' => array( 'projectpress-bootstrap' ),
                'version' => PROJECTPRESS_VERSION,
                'in_footer' => true,
            );
        }

        return $assets;
    }

    /**
     * Register GitHub asset definitions for the frontend context.
     *
     * @param array  $assets The existing asset bundle.
     * @param string $context The current asset context.
     * @return array The updated asset bundle.
     */
    public function register_frontend_assets( array $assets, string $context = 'frontend' ): array {
       
    }

}


