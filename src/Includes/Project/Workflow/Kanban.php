<?php
/**
 * Kanban for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Workflow;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Kanban implementation.
 *
 * @since 1.0.0
 */
final class Kanban {
    /**
     * columns.
     *
     * @param array $items items parameter.
     *
     * @return array columns result.
     *
     * @since 1.0.0
     */
    public static function columns(array $items): array {
        $columns = [];
        foreach ($items as $item) {
            $row = is_object($item) && method_exists($item, "to_array") ? $item->to_array() : (array) $item;
            $status = (string) ($row["status"] ?? "todo");
            $row["planning_type"] = self::type($item, $row);
            $columns[$status][] = $row;
        }
        return $columns;
    }
    /**
     * type.
     *
     * @param mixed $item item parameter.
     * @param array $row row parameter.
     *
     * @return string type result.
     *
     * @since 1.0.0
     */
    private static function type($item, array $row): string {
        if (is_object($item)) {
            $name = get_class($item);
            $parts = explode("\\", $name);
            return strtolower((string) end($parts));
        }
        if (isset($row["task_title"])) {
            return "task";
        }
        if (isset($row["target_date"])) {
            return "milestone";
        }
        return "item";
    }
}
