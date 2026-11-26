<?php

/**
 * Persistence operations for ProjectPress governance records.
 *
 * @package ProjectPress\Includes\Project\Governance
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Governance;

use ProjectPress\Includes\Functions\Helpers\DBHelper;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Governance Repository domain component.
 *
 * @since 1.0.0
 */
final class GovernanceRepository {
    /**
     * Table.
     *
     * @var string
     * @since 1.0.0
     */
    private string $table;
    /**
     * Class.
     *
     * @var string
     * @since 1.0.0
     */
    private string $class;
    /**
     * Construct.
     *
     * @param string $type Type.
     * @return void
     * @since 1.0.0
     */
    public function __construct(string $type) {
        $map = [
            "risk" => ["risks", Risk::class],
            "issue" => ["issues", Issue::class],
            "dependency" => ["dependencies", Dependency::class],
            "decision" => ["decisions", DecisionLog::class],
        ];
        $definition = $map[$type] ?? null;
        if (!$definition) {
            throw new \InvalidArgumentException("Invalid governance record type.");
        }
        $this->table = $definition[0];
        $this->class = $definition[1];
    }
    /**
     * Find.
     *
     * @param int $id Id.
     * @return mixed
     * @since 1.0.0
     */
    public function find(int $id) {
        $row = DBHelper::get_by_id($this->table, absint($id));
        return is_array($row) ? new $this->class($row) : null;
    }
    /**
     * For project.
     *
     * @param int $project_id Project id.
     * @return array
     * @since 1.0.0
     */
    public function for_project(int $project_id): array {
        $class = $this->class;
        return array_map(
            static fn(array $row) => new $class($row),
            DBHelper::get_all($this->table, ["project_id" => absint($project_id)], "*", "created_at DESC"),
        );
    }
    /**
     * Create.
     *
     * @param int $project_id Project id.
     * @param array $data Data.
     * @return mixed
     * @since 1.0.0
     */
    public function create(int $project_id, array $data) {
        $now = current_time("mysql", true);
        $id = DBHelper::insert(
            $this->table,
            array_merge($data, [
                "project_id" => absint($project_id),
                "created_by" => get_current_user_id(),
                "created_at" => $now,
                "updated_by" => get_current_user_id(),
                "updated_at" => $now,
            ]),
        );
        return false === $id ? false : $this->find((int) $id);
    }
    /**
     * Update.
     *
     * @param mixed $record Record.
     * @param array $data Data.
     * @return bool
     * @since 1.0.0
     */
    public function update($record, array $data): bool {
        return DBHelper::update(
            $this->table,
            array_merge($data, ["updated_by" => get_current_user_id(), "updated_at" => current_time("mysql", true)]),
            ["id" => $record->get_id(), "project_id" => $record->get_project_id()],
        );
    }
    /**
     * Delete.
     *
     * @param mixed $record Record.
     * @return bool
     * @since 1.0.0
     */
    public function delete($record): bool {
        return DBHelper::delete($this->table, ["id" => $record->get_id(), "project_id" => $record->get_project_id()]);
    }
}
