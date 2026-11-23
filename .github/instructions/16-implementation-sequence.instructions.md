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

## Phase 9: Advanced Project Management

Phase 9 turns the existing advanced project-domain models into complete end-to-end ProjectPress functionality while preserving the established storage ownership, permission, helper and extension architecture.

### Phase 9.1: Requirements and Backlog
- Implement project requirements, user stories, features and backlog management.
- Add repositories/services and project-scoped persistence for supported records.
- Integrate these records into the individual project workspace and REST API where appropriate.
- Support relationships to existing tasks and milestones without duplicating task or milestone storage.

### Phase 9.2: Workflow and Planning
- Implement project workflows, Kanban views, roadmaps and Gantt planning from canonical project/work data.
- Reuse tasks, milestones, requirements and delivery data rather than introducing competing records.
- Add project-scoped permissions and extension points for planning/workflow integrations.

### Phase 9.3: Governance
- Implement risks, issues, dependencies and decision logs.
- Add project-owned persistence, repositories/services, workspace interfaces and REST behaviour.
- Support relationships to relevant projects and project-owned records while enforcing project isolation.

### Phase 9.4: Quality
- Implement bug and test-case management.
- Support relationships between bugs, test cases, requirements, tasks and delivery records where established by the domain model.
- Add project workspace and REST functionality with consistent permissions and validation.

### Phase 9.5: Delivery
- Implement sprints, releases, deployments and environments.
- Reuse existing task, milestone and project context systems.
- Add project-scoped workspace, service, persistence and REST behaviour.

### Phase 9.6: People and Communication
- Implement timesheets and project notifications using the existing team/member and permission systems.
- Keep team membership authoritative in the existing team repositories rather than introducing duplicate membership storage.
- Add relevant workspace and REST functionality.

### Phase 9.7: Reporting
- Implement project reporting and progress/status reporting over canonical ProjectPress data.
- Keep reports read-oriented and avoid introducing duplicate source-of-truth records.
- Provide extension points for additional report providers.

### Phase 9.8: Composition and SBOM Foundation
- Implement the generic ProjectPress composition model for components, packages, assets, licences, provenance and relationships.
- Provide a generic SBOM representation and extension points without making ProjectPress core dependent on a specific SBOM standard.
- Keep SPDX-specific generation/import/export in a separately installed extension using the ProjectPress composition/SBOM APIs.

### Phase 9.9: Integration, Validation and Documentation
- Connect completed Phase 9 features to the workspace, permissions, REST API and permanent-delete lifecycle as appropriate.
- Add database/repository/service/permission/REST/upgrade-path tests for Phase 9 persistence and behaviour.
- Run PHP syntax, PHPUnit, coding-standard and frontend build validation.
- Update ProjectPress documentation to reflect the completed advanced project-management architecture.
