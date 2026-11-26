<?php

/**
 * Represents a ProjectPress backlog record.
 *
 * @package ProjectPress\Includes\Project\Work
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Work;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Backlog domain component.
 *
 * @since 1.0.0
 */
final class Backlog {
    /**
     * Open.
     *
     * @param array $items Items.
     * @return array
     * @since 1.0.0
     */
    public static function open(array $items): array {
        return array_values(
            array_filter($items, static function ($item) {
                $status =
                    is_object($item) && method_exists($item, "get_status")
                        ? $item->get_status()
                        : (string) ($item["status"] ?? "");
                return !in_array($status, ["done", "completed", "cancelled", "rejected"], true);
            }),
        );
    }
    /**
     * From tasks.
     *
     * @param array $tasks Tasks.
     * @return array
     * @since 1.0.0
     */
    public static function from_tasks(array $tasks): array {
        return self::open($tasks);
    }
}
