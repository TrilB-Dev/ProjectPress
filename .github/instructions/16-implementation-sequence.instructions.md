# 16. Implementation Sequence

Use the following sequence for the initial ProjectPress build.

## Phase 1: Template Conversion
- Identify remaining template placeholders.
- Replace relevant plugin identity values.
- Confirm namespaces/autoloading and bootstrap behaviour.
- Verify activation/deactivation flow.
- Preserve reusable template services.

## Phase 2: Database Foundation
- Review `Database.php`, `Schema.php` and `Defaults.php`.
- Register core tables.
- Define schema versions and installation defaults.
- Confirm clean installation and idempotent schema execution.

## Phase 3: Project Domain
- Create project entity/model, repository and service.
- Add validation, status/priority/visibility definitions, permission services and project-context resolution.

## Phase 4: Global Project Manager
- Add project list, creation/editing, categories/tags, types, global fields, teams/configuration, filtering and authorised bulk operations.

## Phase 5: Individual Project Workspace
- Add overview, details, members, tasks, milestones, discussions, notes, files, activity and project-specific settings.

## Phase 6: REST API
- Add project/related-entity routes, schemas, permission callbacks and consistent responses/errors.

## Phase 7: Extensions and Integration
- Add extension registration points and actions/filters.
- Confirm compatibility with internal plugins and separately installed WordPress extensions.

## Phase 8: Testing and Documentation
- Add database, repository, service/permission, REST and upgrade-path tests.
- Update repository documentation and replace outdated template documentation.
