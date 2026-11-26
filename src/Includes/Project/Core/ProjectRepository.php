<?php
/**
 * Project persistence repository.
 *
 * @package ProjectPress\Includes\Project\Core
 * @since 1.0.0
 */

namespace ProjectPress\Includes\Project\Core;

use ProjectPress\Includes\Core\WP\Database;
use ProjectPress\Includes\Pages\Taxonomies;
use Throwable;

if (!defined("ABSPATH")) {
    exit();
}

/**
 * Reads and writes project records through the approved database layer.
 *
 * @since 1.0.0
 */
final class ProjectRepository {
    /**
     * Project hydrator.
     *
     * @var ProjectHydrator
     * @since 1.0.0
     */
    private ProjectHydrator $hydrator;

    /**
     * Initialise the repository.
     *
     * @param ProjectHydrator|null $hydrator Optional hydrator.
     * @since 1.0.0
     */
    public function __construct(?ProjectHydrator $hydrator = null) {
        $this->hydrator = $hydrator ?? new ProjectHydrator();
    }

    /**
     * Find a project by ID.
     *
     * @param int $id Project ID.
     * @return Project|null
     * @since 1.0.0
     */
    public function find(int $id): ?Project {
        return $this->find_by("id", $id);
    }

    /**
     * Find a project by key.
     *
     * @param string $key Project key.
     * @return Project|null
     * @since 1.0.0
     */
    public function find_by_key(string $key): ?Project {
        return $this->find_by("project_key", $key);
    }

    /**
     * Find a project by slug.
     *
     * @param string $slug Project slug.
     * @return Project|null
     * @since 1.0.0
     */
    public function find_by_slug(string $slug): ?Project {
        return $this->find_by("project_slug", $slug);
    }

    /**
     * Find a project by an allowed column.
     *
     * @param string $column Table column.
     * @param mixed  $value Column value.
     * @return Project|null
     * @since 1.0.0
     */
    private function find_by(string $column, $value): ?Project {
        global $wpdb;

        $table = Database::table_name("projects");
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE {$column} = %s LIMIT 1", $value), ARRAY_A);
        return is_array($row) ? $this->hydrator->hydrate($row) : null;
    }

    /**
     * Query projects.
     *
     * @param ProjectQuery $query Query.
     * @return array
     * @since 1.0.0
     */
    public function query(ProjectQuery $query): array {
        global $wpdb;

        $parts = $this->query_parts($query);
        $args = $query->to_array();
        $table = Database::table_name("projects");
        $allowed = [
            "id",
            "project_name",
            "status",
            "priority",
            "start_date",
            "target_end_date",
            "created_at",
            "updated_at",
        ];
        $order_by = in_array($args["order_by"], $allowed, true) ? $args["order_by"] : "updated_at";
        $offset = ($args["page"] - 1) * $args["per_page"];
        $sql =
            "SELECT DISTINCT p.* FROM {$table} p {$parts["joins"]} WHERE " .
            implode(" AND ", $parts["where"]) .
            " ORDER BY p.{$order_by} {$args["order"]} LIMIT %d OFFSET %d";
        $values = array_merge($parts["values"], [$args["per_page"], $offset]);
        $rows = $wpdb->get_results($wpdb->prepare($sql, $values), ARRAY_A);
        return array_map([$this->hydrator, "hydrate"], is_array($rows) ? $rows : []);
    }

    /**
     * Count projects matching a query.
     *
     * @param ProjectQuery $query Query.
     * @return int
     * @since 1.0.0
     */
    public function count(ProjectQuery $query): int {
        global $wpdb;

        $parts = $this->query_parts($query);
        $table = Database::table_name("projects");
        $sql =
            "SELECT COUNT(DISTINCT p.id) FROM {$table} p {$parts["joins"]} WHERE " . implode(" AND ", $parts["where"]);
        return (int) ($parts["values"] ? $wpdb->get_var($wpdb->prepare($sql, $parts["values"])) : $wpdb->get_var($sql));
    }

    /**
     * Build project query clauses.
     *
     * @param ProjectQuery $query Query.
     * @return array
     * @since 1.0.0
     */
    private function query_parts(ProjectQuery $query): array {
        global $wpdb;

        $args = $query->to_array();
        $where = ["1=1"];
        $where_values = [];
        $join_values = [];
        $joins = "";

        foreach (["status", "priority", "visibility"] as $field) {
            if ("" !== $args[$field]) {
                $where[] = "p.{$field} = %s";
                $where_values[] = $args[$field];
            }
        }
        foreach (["owner_id", "parent_id"] as $field) {
            if ($args[$field] > 0) {
                $where[] = "p.{$field} = %d";
                $where_values[] = $args[$field];
            }
        }
        if ("" !== $args["search"]) {
            $like = "%" . $wpdb->esc_like($args["search"]) . "%";
            $where[] = "(p.project_name LIKE %s OR p.project_description LIKE %s OR p.project_key LIKE %s)";
            array_push($where_values, $like, $like, $like);
        }

        $filters = [
            "project_type" => Taxonomies::PROJECT_TYPE,
            "project_category" => Taxonomies::PROJECT_CATEGORY,
            "project_tag" => Taxonomies::PROJECT_TAG,
            "project_label" => Taxonomies::PROJECT_LABEL,
        ];
        $i = 0;
        foreach ($filters as $key => $taxonomy) {
            if ($args[$key] > 0) {
                $alias = "ptr" . ++$i;
                $joins .=
                    " INNER JOIN " .
                    Database::table_name("project_term_relationships") .
                    " {$alias} ON {$alias}.project_id = p.id AND {$alias}.taxonomy = %s AND {$alias}.term_id = %d";
                $join_values[] = $taxonomy;
                $join_values[] = $args[$key];
            }
        }

        return ["joins" => $joins, "where" => $where, "values" => array_merge($join_values, $where_values)];
    }

    /**
     * Insert a project.
     *
     * @param Project $project Project entity.
     * @return int|false
     * @since 1.0.0
     */
    public function insert(Project $project) {
        global $wpdb;

        $data = array_filter($this->hydrator->extract($project), static fn($value): bool => null !== $value);
        $data["created_at"] = current_time("mysql", true);
        $data["updated_at"] = $data["created_at"];
        $result = $wpdb->insert(Database::table_name("projects"), $data);
        return false === $result ? false : (int) $wpdb->insert_id;
    }

    /**
     * Update a project.
     *
     * @param Project $project Project entity.
     * @return bool
     * @since 1.0.0
     */
    public function update(Project $project): bool {
        if ($project->get_id() < 1) {
            return false;
        }
        global $wpdb;

        $data = $this->hydrator->extract($project);
        unset($data["created_by"]);
        $data["updated_at"] = current_time("mysql", true);
        return false !== $wpdb->update(Database::table_name("projects"), $data, ["id" => $project->get_id()]);
    }

    /**
     * Permanently delete a project and owned records.
     *
     * @param int $id Project ID.
     * @return bool
     * @since 1.0.0
     */
    public function delete(int $id): bool {
        global $wpdb;

        $id = absint($id);
        if ($id < 1) {
            return false;
        }

        $wpdb->query("START TRANSACTION");
        try {
            $children_updated = $wpdb->update(
                Database::table_name("projects"),
                ["parent_id" => null],
                ["parent_id" => $id],
                ["%d"],
                ["%d"],
            );
            if (false === $children_updated) {
                throw new \RuntimeException("Could not detach child projects.");
            }

            $tables = [
                "project_term_relationships",
                "project_teams",
                "project_field_values",
                "activity",
                "tasks",
                "milestones",
                "requirements",
                "features",
                "user_stories",
                "risks",
                "issues",
                "dependencies",
                "decisions",
                "bugs",
                "test_cases",
                "sprints",
                "releases",
                "deployments",
                "environments",
                "timesheets",
                "project_notifications",
                "components",
                "packages",
                "composition_assets",
                "composition_licences",
                "attributions",
                "provenance",
                "composition_relationships",
                "sboms",
                "discussions",
                "notes",
                "files",
                "project_settings",
            ];
            foreach ($tables as $table) {
                if (false === $wpdb->delete(Database::table_name($table), ["project_id" => $id], ["%d"])) {
                    throw new \RuntimeException("Could not remove project-owned records.");
                }
            }

            $relationships = Database::table_name("project_relationships");
            $relationship_result = $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$relationships} WHERE source_project_id = %d OR target_project_id = %d",
                    $id,
                    $id,
                ),
            );
            if (false === $relationship_result) {
                throw new \RuntimeException("Could not remove project relationships.");
            }

            $deleted = $wpdb->delete(Database::table_name("projects"), ["id" => $id], ["%d"]);
            if (1 !== $deleted) {
                throw new \RuntimeException("Could not remove project.");
            }
            $wpdb->query("COMMIT");
            return true;
        } catch (Throwable $error) {
            $wpdb->query("ROLLBACK");
            return false;
        }
    }
}
