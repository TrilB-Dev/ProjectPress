<?php

/**
 * Persistence operations for ProjectPress delivery records.
 *
 * @package ProjectPress\Includes\Project\Delivery
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Delivery;

use ProjectPress\Includes\Functions\Helpers\DBHelper;

if (!defined("ABSPATH")) {
    exit();
}
/**
 * Delivery Repository domain component.
 *
 * @since 1.0.0
 */
final class DeliveryRepository {
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
            "sprint" => ["sprints", Sprint::class],
            "release" => ["releases", Release::class],
            "deployment" => ["deployments", Deployment::class],
            "environment" => ["environments", Environment::class],
        ];
        if (!isset($map[$type])) {
            throw new \InvalidArgumentException("Invalid delivery type.");
        }
        $this->table = $map[$type][0];
        $this->class = $map[$type][1];
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
     * @param int $id Id.
     * @return array
     * @since 1.0.0
     */
    public function for_project(int $id): array {
        $class = $this->class;
        return array_map(
            static fn(array $row) => new $class($row),
            DBHelper::get_all($this->table, ["project_id" => absint($id)], "*", "created_at DESC"),
        );
    }
    /**
     * Create.
     *
     * @param int $id Id.
     * @param array $data Data.
     * @return mixed
     * @since 1.0.0
     */
    public function create(int $id, array $data) {
        $now = current_time("mysql", true);
        $record = DBHelper::insert(
            $this->table,
            array_merge($data, [
                "project_id" => absint($id),
                "created_by" => get_current_user_id(),
                "created_at" => $now,
                "updated_by" => get_current_user_id(),
                "updated_at" => $now,
            ]),
        );
        return false === $record ? false : $this->find((int) $record);
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
