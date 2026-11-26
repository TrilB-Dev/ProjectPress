<?php
/**
 * ProjectExtensions for ProjectPress.
 *
 * @package ProjectPress
 * @since   1.0.0
 */
namespace ProjectPress\Includes\Project\Integration;

use ProjectPress\Includes\Plugins\Plugins;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
if (!defined("ABSPATH")) {
    exit();
}
/**
 * ProjectExtensions implementation.
 *
 * @since 1.0.0
 */
final class ProjectExtensions {
    /**
     * registered property.
     *
     * @var bool
     * @since 1.0.0
     */
    private static bool $registered = false;
    /**
     * register.
     *
     * @return void register result.
     *
     * @since 1.0.0
     */
    public static function register(): void {
        if (self::$registered) {
            return;
        }
        self::$registered = true;
        $registry = ProjectExtensionRegistry::get_instance();
        foreach (Plugins::get_instance()->get_registered_plugins() as $plugin) {
            if ($plugin instanceof ProjectExtensionProviderInterface) {
                $plugin->register_project_extensions($registry);
            }
            if ($plugin instanceof ProjectWorkspaceProviderInterface) {
                foreach ($plugin->get_project_workspace_tabs() as $key => $definition) {
                    $registry->add_workspace_tab((string) $key, (array) $definition);
                }
            }
            if ($plugin instanceof ProjectRelatedEntityProviderInterface) {
                foreach ($plugin->get_project_related_entities() as $key => $definition) {
                    $registry->add_related_entity((string) $key, (array) $definition);
                }
            }
        }
        do_action("projectpress_project_extensions_registered", $registry, Plugins::get_instance());
    }
    /**
     * rest_response.
     *
     * @param mixed $response response parameter.
     * @param WP_REST_Server $server server parameter.
     * @param WP_REST_Request $request request parameter.
     *
     * @return mixed rest_response result.
     *
     * @since 1.0.0
     */
    public static function rest_response($response, WP_REST_Server $server, WP_REST_Request $request) {
        if (
            !($response instanceof WP_REST_Response) ||
            0 !== strpos($request->get_route(), "/projectpress/v1/projects/")
        ) {
            return $response;
        }
        $body = $response->get_data();
        if (
            !is_array($body) ||
            empty($body["success"]) ||
            !isset($body["data"]) ||
            !is_array($body["data"]) ||
            empty($body["data"]["id"])
        ) {
            return $response;
        }
        $project = \ProjectPress\Includes\Project\ProjectManager::get_instance()->find(absint($body["data"]["id"]));
        if ($project) {
            $body["data"] = ProjectExtensionRegistry::get_instance()->extend_project_data($body["data"], $project);
            $response->set_data($body);
        }
        return $response;
    }
    /**
     * workspace_tabs.
     *
     * @param array $tabs tabs parameter.
     *
     * @return array workspace_tabs result.
     *
     * @since 1.0.0
     */
    public static function workspace_tabs(array $tabs): array {
        return array_merge($tabs, ProjectExtensionRegistry::get_instance()->workspace_tabs());
    }
    /**
     * related_entities.
     *
     * @param array $entities entities parameter.
     *
     * @return array related_entities result.
     *
     * @since 1.0.0
     */
    public static function related_entities(array $entities): array {
        return array_merge($entities, ProjectExtensionRegistry::get_instance()->related_entities());
    }
}
