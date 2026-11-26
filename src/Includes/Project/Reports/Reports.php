<?php
/**
 * Reports for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Reports;

if (!defined("ABSPATH")) {
    exit();
}
final class Reports {
    /**
     * count_by.
     *
     * @param array $rows rows parameter.
     * @param string $field field parameter.
     *
     * @return array count_by result.
     *
     * @since 1.0.0
     */
    public static function count_by(array $rows, string $field): array {
        $counts = [];
        foreach ($rows as $row) {
            $row = is_object($row) && method_exists($row, "to_array") ? $row->to_array() : (array) $row;
            $key = (string) ($row[$field] ?? "unknown");
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }
        return $counts;
    }
}
