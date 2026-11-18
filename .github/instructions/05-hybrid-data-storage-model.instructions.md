# 5. Hybrid Data-Storage Model

ProjectPress must use a hybrid storage architecture.

## 5.1 Custom Tables

Custom tables are used for structured, relational, frequently queried or operational data. The primary project record belongs in a custom projects table.

The project table should support the approved project fields, including internal numeric ID, name, slug, unique key, description, parent project, team, project type, owner, start/target/actual end dates, status, priority, visibility, approved structured member/field data, creator, creation timestamp and update timestamp.

Existing approved schema names and types must be preserved unless a deliberate migration is implemented. Additional custom tables should be used where relational/operational behaviour warrants them, such as project relationships, memberships, tasks, milestones, dependencies, activity logs, time records, notifications and structured field definitions/values.

## 5.2 WordPress Posts

Use WordPress posts for content-centric entities benefiting from the native content lifecycle, such as discussions, notes, documentation, project updates and other editorial records requiring authorship, revisions, publication states or native media relationships.

Do not create a WordPress post merely to mirror a custom-table record. Each entity must have one authoritative storage location.

## 5.3 Post Metadata

Post metadata may store values belonging specifically to a post-based ProjectPress entity, including associated project ID, entity-specific status/visibility, related milestone/task IDs, external references and post-specific display/workflow values.

Frequently filtered values should not be placed in post metadata without considering query performance and indexing.

## 5.4 Taxonomies

Use taxonomies for reusable classification, such as project categories, project tags, discussion categories, document categories and reusable labels. Do not use taxonomies as a replacement for relational membership, ownership or permissions.

## 5.5 Term Metadata

Term metadata may store term-specific properties such as colour, icon, display order, visibility behaviour and classification settings. Do not duplicate project-specific values across term metadata and the primary project table.

## 5.6 WordPress Users

ProjectPress users must remain WordPress users. Do not create a parallel user-identity table. Membership, team roles and project permissions must reference WordPress user IDs through the approved ProjectPress storage mechanism.
