<?php

/**
 * Business services for ProjectPress task records.
 *
 * @package ProjectPress\Includes\Project\Work
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Work;

use ProjectPress\Includes\Project\Core\ProjectPermissions;
use ProjectPress\Includes\Project\Core\ProjectRepository;
use WP_Error;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Task Service domain component.
 *
 * @since 1.0.0
 */
final class TaskService {
    /**
     * Tasks.
     *
     * @var TaskRepository
     * @since 1.0.0
     */
    private TaskRepository $tasks;
    /**
     * Projects.
     *
     * @var ProjectRepository
     * @since 1.0.0
     */
    private ProjectRepository $projects;
    /**
     * Permissions.
     *
     * @var ProjectPermissions
     * @since 1.0.0
     */
    private ProjectPermissions $permissions;
    /**
     * Construct.
     *
     * @param TaskRepository $tasks Tasks.
     * @param ProjectRepository $projects Projects.
     * @param ProjectPermissions $permissions Permissions.
     * @return void
     * @since 1.0.0
     */
    public function __construct(TaskRepository $tasks, ProjectRepository $projects, ProjectPermissions $permissions) {
        $this->tasks = $tasks;
        $this->projects = $projects;
        $this->permissions = $permissions;
    }
    /**
     * Statuses.
     *
     * @return array
     * @since 1.0.0
     */
    public static function statuses(): array {
        return ["todo", "in_progress", "blocked", "done"];
    }
    /**
     * Priorities.
     *
     * @return array
     * @since 1.0.0
     */
    public static function priorities(): array {
        return ["low", "normal", "high", "urgent"];
    }
    /**
     * Create.
     *
     * @param int $project_id Project id.
     * @param array $input Input.
     * @return mixed
     * @since 1.0.0
     */
    public function create(int $project_id, array $input) {
        $project = $this->projects->find($project_id);
        if (!$project || !$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_task_forbidden",
                __("You do not have permission to add tasks to this project.", "projectpress"),
            );
        }
        $data = $this->validate($input);
        if (is_wp_error($data)) {
            return $data;
        }
        $data["project_id"] = $project_id;
        $data["created_by"] = get_current_user_id();
        $data["created_at"] = current_time("mysql", true);
        $data["updated_by"] = get_current_user_id();
        $data["updated_at"] = $data["created_at"];
        $id = $this->tasks->create($data);
        return false === $id
            ? new WP_Error("projectpress_task_create_failed", __("The task could not be created.", "projectpress"))
            : $this->tasks->find((int) $id);
    }
    /**
     * Update.
     *
     * @param Task $task Task.
     * @param array $input Input.
     * @return mixed
     * @since 1.0.0
     */
    public function update(Task $task, array $input) {
        $project = $this->projects->find($task->get_project_id());
        if (!$project || !$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_task_forbidden",
                __("You do not have permission to edit tasks in this project.", "projectpress"),
            );
        }
        $data = $this->validate($input, true);
        if (is_wp_error($data)) {
            return $data;
        }
        $data["updated_by"] = get_current_user_id();
        $data["updated_at"] = current_time("mysql", true);
        return $this->tasks->update($task->get_id(), $data)
            ? $this->tasks->find($task->get_id())
            : new WP_Error("projectpress_task_update_failed", __("The task could not be updated.", "projectpress"));
    }
    /**
     * Delete.
     *
     * @param Task $task Task.
     * @return mixed
     * @since 1.0.0
     */
    public function delete(Task $task) {
        $project = $this->projects->find($task->get_project_id());
        if (!$project || !$this->permissions->can_edit($project)) {
            return new WP_Error(
                "projectpress_task_forbidden",
                __("You do not have permission to delete tasks in this project.", "projectpress"),
            );
        }
        return $this->tasks->delete($task->get_id())
            ? true
            : new WP_Error("projectpress_task_delete_failed", __("The task could not be deleted.", "projectpress"));
    }
    /**
     * Validate.
     *
     * @param array $input Input.
     * @param bool $partial Partial.
     * @return mixed
     * @since 1.0.0
     */
    private function validate(array $input, bool $partial = false) {
        $data = [];
        if (!$partial || array_key_exists("title", $input)) {
            $data["task_title"] = sanitize_text_field($input["title"] ?? "");
            if ("" === $data["task_title"]) {
                return new WP_Error(
                    "projectpress_task_title_required",
                    __("A task title is required.", "projectpress"),
                );
            }
        }
        if (array_key_exists("description", $input)) {
            $data["task_description"] = sanitize_textarea_field($input["description"]);
        }
        foreach (["status" => self::statuses(), "priority" => self::priorities()] as $key => $allowed) {
            if (array_key_exists($key, $input)) {
                $value = sanitize_key($input[$key]);
                if (!in_array($value, $allowed, true)) {
                    return new WP_Error(
                        "projectpress_task_" . $key . "_invalid",
                        __("The task value is invalid.", "projectpress"),
                    );
                }
                $data[$key] = $value;
            }
        }
        if (array_key_exists("assignee_id", $input)) {
            $id = absint($input["assignee_id"]);
            if ($id && !get_user_by("id", $id)) {
                return new WP_Error(
                    "projectpress_task_assignee_invalid",
                    __("The selected assignee does not exist.", "projectpress"),
                );
            }
            $data["assignee_id"] = $id ?: null;
        }
        if (array_key_exists("due_date", $input)) {
            $value = sanitize_text_field($input["due_date"]);
            if ("" !== $value && false === strtotime($value)) {
                return new WP_Error(
                    "projectpress_task_due_invalid",
                    __("The task due date is invalid.", "projectpress"),
                );
            }
            $data["due_date"] = "" === $value ? null : gmdate("Y-m-d H:i:s", strtotime($value));
        }
        return $data;
    }
}
