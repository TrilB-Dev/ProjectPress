<?php

namespace ProjectPress\Includes;

use ProjectPress\Includes\Core\Capabilities;
use ProjectPress\Includes\Core\WP\WPLoader;
use ProjectPress\Includes\Functions\Helpers\LoggerHelper;
use ProjectPress\Includes\Pages\CronJobs;
use ProjectPress\Includes\Pages\PostTypes;
use ProjectPress\Includes\Pages\Shortcodes;
use ProjectPress\Includes\Pages\Taxonomies;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Includes {
    /** @var self|null */
    private static ?self $instance = null;

    /** @var array */
    private array $extensions = array();

    private bool $initialized = false;

    private function __construct() {
        LoggerHelper::write_log( 'ProjectPress includes initialized.' );
    }

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    public function register_core(): void {
        if ( $this->initialized ) {
            return;
        }

        Capabilities::install();
        PostTypes::register();
        Taxonomies::register();
        Shortcodes::register();
        CronJobs::register();

        $this->initialized = true;
    }

    public function init(): void {
        $this->register_core();

        foreach ( $this->extensions as $extension ) {
            call_user_func( $extension, $this );
        }
    }

    public function register_extension( callable $extension ): self {
        if ( $this->initialized ) {
            call_user_func( $extension, $this );
        } else {
            $this->extensions[] = $extension;
        }

        return $this;
    }

    public function register_hooks( WPLoader $loader, string $hook = 'init', int $priority = 10 ): self {
        $loader->add_action( $hook, $this, 'init', $priority, 0 );
        return $this;
    }

    public function is_initialized(): bool {
        return $this->initialized;
    }
}
