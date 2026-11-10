<?php
/**
 * This file manages the internationalization functionality of the plugin.
 * 
 * 
 * 
 * @package ProjectPress\Includes\Plugins\GitHub\Includes
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Plugins\GitHub\Includes\Core;

final class I18n {
    public static function load_textdomain(): void {
        load_plugin_textdomain(
            'projectpress',
            false,
            dirname( plugin_basename( PROJECTPRESS_PLUGINS ) ) . '/GitHub/Language/'
        );
    }
}


