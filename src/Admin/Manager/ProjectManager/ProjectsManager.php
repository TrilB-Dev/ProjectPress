<?php
/**
 * Project Manager administration controller.
 *
 * @package ProjectPress
 * @subpackage Admin\Manager\ProjectManager
 * @since 1.0.0
 */

namespace ProjectPress\Admin\Manager\ProjectManager;

use ProjectPress\Admin\Manager\Manager;
use ProjectPress\Assets\Assets;
use ProjectPress\Includes\Functions\Helpers\RequestHelper;
use ProjectPress\Includes\Project\ProjectManager as DomainProjectManager;

if (!defined("ABSPATH")) {
    exit();
}

final class ProjectsManager extends Manager {
    /**
     * Project list and form handler.
     *
     * @var Projects
     */
    private Projects $projects;

    /**
     * Category handler.
     *
     * @var Categories
     */
    private Categories $categories;

    /**
     * Tag handler.
     *
     * @var Tags
     */
    private Tags $tags;

    /**
     * Type handler.
     *
     * @var Types
     */
    private Types $types;

    /**
     * Label handler.
     *
     * @var Labels
     */
    private Labels $labels;

    /**
     * Team handler.
     *
     * @var Teams
     */
    private Teams $teams;

    /**
     * Field handler.
     *
     * @var Fields
     */
    private Fields $fields;
    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->page = "project-manager";
        $domain = DomainProjectManager::get_instance();
        $this->projects = new Projects($domain);
        $this->categories = new Categories();
        $this->tags = new Tags();
        $this->types = new Types();
        $this->labels = new Labels();
        $this->teams = new Teams();
        $this->fields = new Fields();

        $this->register_actions();
    }

    /**
     * Registers admin post actions for handling project-related requests.
     *
     * @return void
     * @since 1.0.0
     */
    private function register_actions(): void {
        add_action("admin_post_projectpress_project_save", [$this->projects, "handle_save"]);
        add_action("admin_post_projectpress_project_archive", [$this->projects, "handle_archive"]);
        add_action("admin_post_projectpress_project_restore", [$this->projects, "handle_restore"]);
        add_action("admin_post_projectpress_project_delete", [$this->projects, "handle_delete"]);
        add_action("admin_post_projectpress_projects_bulk", [$this->projects, "handle_bulk"]);
        add_action("admin_post_projectpress_classification_save", [$this->types, "handle_save"]);
        add_action("admin_post_projectpress_team_save", [$this->teams, "handle_save"]);
        add_action("admin_post_projectpress_team_member_save", [$this->teams, "handle_member_save"]);
        add_action("admin_post_projectpress_team_project_save", [$this->teams, "handle_project_save"]);
        add_action("admin_post_projectpress_field_save", [$this->fields, "handle_save"]);
    }

    /**
     * Registers assets for the project manager pages.
     *
     * @param Assets $assets The assets manager instance.
     * @return void
     * @since 1.0.0
     */
    public function register_assets(Assets $assets): void {
        $this->register_page_assets($assets, ["projectpress"], "projects");
    }
    /**
     * Renders the project manager page based on the current tab and form.
     *
     * @param string $tab The current tab.
     * @param string $form The current form.
     * @return void
     * @since 1.0.0
     */
    public function render(string $tab = "", string $form = ""): void {
        if (!current_user_can("manage_options") && !current_user_can("projectpress_projects_view")) {
            wp_die(esc_html__("You are not authorised to view Project Manager.", "projectpress"));
        }

        $allowed_tabs = ["projects", "new", "edit", "categories", "tags", "types", "labels", "fields", "teams"];
        $tab = $this->normalize_route(
            "" !== $tab ? $tab : RequestHelper::get_key("tab", "projects"),
            array_combine($allowed_tabs, $allowed_tabs),
            "projects",
        );

        $this->header(__("Project Manager", "projectpress"));

        switch ($tab) {
            case "new":
                $this->projects->render_form();
                break;
            case "edit":
                $this->projects->render_form(RequestHelper::get_integer("project_id", 0));
                break;
            case "categories":
                $this->categories->render();
                break;
            case "tags":
                $this->tags->render();
                break;
            case "types":
                $this->types->render();
                break;
            case "labels":
                $this->labels->render();
                break;
            case "fields":
                $this->fields->render();
                break;
            case "teams":
                $this->teams->render();
                break;
            default:
                $this->projects->render();
        }

        $this->footer();
    }
}
