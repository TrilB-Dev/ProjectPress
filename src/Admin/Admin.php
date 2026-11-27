<?php
/**
 * Admin class for ProjectPress plugin.
 *
 * @package ProjectPress
 * @subpackage Admin
 * @since 1.0.0
 */
namespace ProjectPress\Admin;

use ProjectPress\Includes\Settings\Settings;
use ProjectPress\Includes\Functions\Helpers\AjaxHelper;
use ProjectPress\Includes\Core\Capabilities;
use ProjectPress\Includes\Functions\Helpers\LoaderHelper;
use ProjectPress\Includes\Functions\Helpers\LoggerHelper;
use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Functions\Helpers\SanitizationHelper;
use ProjectPress\Includes\Functions\Admin\FunctionsSidebar;
use ProjectPress\Assets\Assets;
use ProjectPress\Admin\Manager\Manager;
use ProjectPress\Admin\Manager\Tools\ToolsManager;
use ProjectPress\Admin\Manager\Dashboard\DashboardManager;
use ProjectPress\Admin\Manager\Projects\ProjectManager;
use ProjectPress\Admin\Manager\Settings\SettingsManager;
use ProjectPress\Admin\Manager\Tools\Export;
use ProjectPress\Admin\Manager\Tools\General;
use ProjectPress\Admin\Manager\Tools\Import;
use ProjectPress\Admin\Manager\Tools\Reset;

if (!defined("ABSPATH")) {
    exit();
}

final class Admin {
    /**
     * Dashboard manager instance.
     *
     * @var DashboardManager
     * @since 1.0.0
     */
    private DashboardManager $dashboard_manager;
    /**
     * Settings manager instance.
     *
     * @var SettingsManager
     * @since 1.0.0
     */
    private SettingsManager $settings_manager;

    /**
     * Project manager instance.
     *
     * @var ProjectManager
     * @since 1.0.0
     */
    private ProjectManager $projects_manager;
    /**
     * Tools manager instance.
     *
     * @var ToolsManager
     * @since 1.0.0
     */
    private ToolsManager $tools_manager;
    /**
     * Array of all manager instances.
     *
     * @var array
     * @since 1.0.0
     */
    private array $managers;
    /**
     * Loader helper instance.
     *
     * @var LoaderHelper
     * @since 1.0.0
     */
    private LoaderHelper $loader;
    /**
     * Assets instance.
     *
     * @var Assets
     * @since 1.0.0
     */
    private Assets $assets;

    public function __construct(Assets $assets) {
        $project_manager = new ProjectManager();
        $this->managers = [
            "dashboard" => new DashboardManager(),
            "project-manager" => $project_manager,
            "projects" => $project_manager,
            "settings" => new SettingsManager(),
            "tools" => new ToolsManager(),
        ];
        $this->dashboard_manager = $this->managers["dashboard"];
        $this->projects_manager = $this->managers["project-manager"];
        $this->settings_manager = $this->managers["settings"];
        $this->tools_manager = $this->managers["tools"];
        $this->loader = new LoaderHelper();
        new General($this->loader);
        new Reset($this->loader);
        new Import($this->loader);
        new Export($this->loader);
        $this->assets = $assets;
        $registered_managers = [];
        foreach ($this->managers as $manager) {
            if (in_array($manager, $registered_managers, true)) {
                continue;
            }
            $registered_managers[] = $manager;
            $manager->register_assets($assets);
        }
        $this->loader->register_component($this, [
            [
                "type" => "action",
                "hook" => "wp_ajax_projectpress_dismiss_onboarding",
                "callback" => "dismiss_onboarding",
            ],
        ]);
        $this->loader->run();
    }

    public function register_admin_menu(): void {
        LoggerHelper::write_log("ProjectPress admin menu registration started.");
        try {
            FunctionsSidebar::register_admin_menu($this);
            LoggerHelper::write_log("ProjectPress admin menu registration complete.");
        } catch (\Throwable $e) {
            LoggerHelper::write_log("ProjectPress admin menu registration failed: " . $e->getMessage());
            if (defined("WP_DEBUG") && WP_DEBUG) {
                wp_die(esc_html($e->getMessage()), __("ProjectPress admin menu error", "projectpress"), [
                    "back_link" => true,
                ]);
            }
        }
    }

    public function render_dashboard(): void {
        $this->route_manager(RequestHelper::get_key("group", ""), RequestHelper::get_key("tab", ""));
    }

    public function route_manager(string $group, string $tab = "", string $form = ""): void {
        $group = sanitize_key($group);
        $tab = sanitize_key($tab);
        $form = sanitize_key($form);
        $normalized_group =
            [
                "projectpress" => "dashboard",
                "dashboard" => "dashboard",
                "project-manager" => "project-manager",
                "projects" => "projects",
                "settings" => "settings",
                "tools" => "tools",
                "reports" => "reports",
            ][$group] ?? "dashboard";
        try {
            switch ($normalized_group) {
                case "project-manager":
                    $this->render_projects($tab, $form);
                    return;
                case "projects":
                    $this->render_project_workspace($tab, $form);
                    return;
                case "settings":
                    $this->render_settings();
                    return;
                case "tools":
                    $this->render_tools();
                    return;
                default:
                    $this->dashboard_manager->render();
            }
        } catch (\Throwable $e) {
            LoggerHelper::write_log("ProjectPress dashboard render failed: " . $e->getMessage());
            if (defined("WP_DEBUG") && WP_DEBUG) {
                wp_die(esc_html($e->getMessage()), __("ProjectPress dashboard error", "projectpress"), [
                    "back_link" => true,
                ]);
            }
        }
    }

    public function dismiss_onboarding(): void {
        if (!AjaxHelper::authorized("projectpress_dismiss_onboarding", "manage_options")) {
            AjaxHelper::unauthorized(
                __("You are not authorized to dismiss the ProjectPress onboarding modal.", "projectpress"),
            );
        }
        Settings::register_group("setup", ["first_install_complete" => false]);
        Settings::set("first_install_complete", true);
        Settings::set("onboarding_steps_complete", 3);
        AjaxHelper::success(["dismissed" => true]);
    }

    /**
     * Render the global Project Manager route.
     *
     * @param string $tab Active global tab.
     * @param string $form Active form route.
     * @return void
     * @since 1.0.0
     */
    public function render_projects(string $tab = "", string $form = ""): void {
        $this->projects_manager->render_global($tab, $form);
    }

    /**
     * Render an individual project workspace route.
     *
     * @param string $tab Active workspace tab.
     * @param string $form Active form route.
     * @return void
     * @since 1.0.0
     */
    public function render_project_workspace(string $tab = "", string $form = ""): void {
        $this->projects_manager->render_workspace($tab, $form);
    }
    public function render_settings(): void {
        $this->settings_manager->render();
    }
    public function render_tools(): void {
        $this->tools_manager->render();
    }
    public function capability(string $key, string $fallback): string {
        $value = Settings::get($key, $fallback);
        $values = is_array($value) ? $value : [$value];
        $allowed = array_merge(
            ["manage_options", "edit_posts", "publish_posts", "manage_categories", "delete_posts"],
            array_keys(Capabilities::definitions()),
        );
        foreach ($values as $value) {
            $capability = SanitizationHelper::key($value, $fallback);
            if (in_array($capability, $allowed, true)) {
                return $capability;
            }
        }
        return $fallback;
    }
}
