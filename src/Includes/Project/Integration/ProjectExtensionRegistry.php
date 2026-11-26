<?php
/**
 * ProjectExtensionRegistry for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Integration;

use ProjectPress\Includes\Project\Core\Project;
if (!defined("ABSPATH")) {
    exit();
}
/**
 * ProjectExtensionRegistry implementation.
 *
 * @since 1.0.0
 */
final class ProjectExtensionRegistry {
    /**
     * instance property.
     *
     * @var ?self
     * @since 1.0.0
     */
    private static ?self $instance = null;
    /**
     * workspace_tabs property.
     *
     * @var array
     * @since 1.0.0
     */
    private array $workspace_tabs = [];
    /**
     * related_entities property.
     *
     * @var array
     * @since 1.0.0
     */
    private array $related_entities = [];
    /**
     * serializers property.
     *
     * @var array
     * @since 1.0.0
     */
    private array $serializers = [];
    /**
     * get_instance.
     *
     * @return self get_instance result.
     *
     * @since 1.0.0
     */
    public static function get_instance(): self {
        return self::$instance ??= new self();
    }
    /**
     * add_workspace_tab.
     *
     * @param string $key key parameter.
     * @param array $definition definition parameter.
     *
     * @return bool add_workspace_tab result.
     *
     * @since 1.0.0
     */
    public function add_workspace_tab(string $key, array $definition): bool {
        $key = sanitize_key($key);
        if (
            "" === $key ||
            !isset($definition["label"], $definition["render_callback"]) ||
            !is_callable($definition["render_callback"])
        ) {
            return false;
        }
        $this->workspace_tabs[$key] = [
            "label" => sanitize_text_field((string) $definition["label"]),
            "render_callback" => $definition["render_callback"],
            "permission_callback" =>
                isset($definition["permission_callback"]) && is_callable($definition["permission_callback"])
                    ? $definition["permission_callback"]
                    : null,
            "priority" => (int) ($definition["priority"] ?? 100),
        ];
        return true;
    }
    /**
     * add_related_entity.
     *
     * @param string $key key parameter.
     * @param array $definition definition parameter.
     *
     * @return bool add_related_entity result.
     *
     * @since 1.0.0
     */
    public function add_related_entity(string $key, array $definition): bool {
        $key = sanitize_key($key);
        if ("" === $key || empty($definition["table"])) {
            return false;
        }
        $this->related_entities[$key] = [
            "table" => sanitize_key((string) $definition["table"]),
            "order_by" => sanitize_text_field((string) ($definition["order_by"] ?? "created_at DESC")),
            "create_callback" =>
                isset($definition["create_callback"]) && is_callable($definition["create_callback"])
                    ? $definition["create_callback"]
                    : null,
            "delete_callback" =>
                isset($definition["delete_callback"]) && is_callable($definition["delete_callback"])
                    ? $definition["delete_callback"]
                    : null,
        ];
        return true;
    }
    /**
     * add_project_serializer.
     *
     * @param string $key key parameter.
     * @param callable $callback callback parameter.
     *
     * @return bool add_project_serializer result.
     *
     * @since 1.0.0
     */
    public function add_project_serializer(string $key, callable $callback): bool {
        $key = sanitize_key($key);
        if ("" === $key) {
            return false;
        }
        $this->serializers[$key] = $callback;
        return true;
    }
    /**
     * workspace_tabs.
     *
     * @return array workspace_tabs result.
     *
     * @since 1.0.0
     */
    public function workspace_tabs(): array {
        $tabs = $this->workspace_tabs;
        uasort($tabs, static fn($a, $b) => $a["priority"] <=> $b["priority"]);
        return apply_filters("projectpress_extension_workspace_tabs", $tabs);
    }
    /**
     * related_entities.
     *
     * @return array related_entities result.
     *
     * @since 1.0.0
     */
    public function related_entities(): array {
        return apply_filters("projectpress_extension_related_entities", $this->related_entities);
    }
    /**
     * extend_project_data.
     *
     * @param array $data data parameter.
     * @param Project $project project parameter.
     *
     * @return array extend_project_data result.
     *
     * @since 1.0.0
     */
    public function extend_project_data(array $data, Project $project): array {
        foreach ($this->serializers as $key => $callback) {
            $data[$key] = call_user_func($callback, $project);
        }
        return apply_filters("projectpress_rest_project_data", $data, $project);
    }
}
