<?php
/**
 * RoadMap for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Planning;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * RoadMap implementation.
 *
 * @since 1.0.0
 */
final class RoadMap {
    /**
     * group_by_status.
     *
     * @param array $items items parameter.
     *
     * @return array group_by_status result.
     *
     * @since 1.0.0
     */
    public static function group_by_status(array $items): array {
        $groups = [];
        foreach ($items as $item) {
            $row = is_object($item) && method_exists($item, "to_array") ? $item->to_array() : (array) $item;
            $status = (string) ($row["status"] ?? "unscheduled");
            $groups[$status][] = $row;
        }
        return $groups;
    }
    /**
     * items.
     *
     * @param array $items items parameter.
     *
     * @return array items result.
     *
     * @since 1.0.0
     */
    public static function items(array $items): array {
        return array_values(
            array_map(
                static fn($item) => is_object($item) && method_exists($item, "to_array")
                    ? $item->to_array()
                    : (array) $item,
                $items,
            ),
        );
    }
}
