# ProjectPress New Element Integration Audit

This audit tracks the integration work required after the Project domain model expansion.

## Integration rule

A domain model is not considered feature-complete merely because the PHP class exists. Each user-facing ProjectPress entity must be mapped deliberately through the appropriate persistence, service, permission, API, and UI layers.

The intended flow is:

`Domain model -> repository/storage -> service/validation -> permissions -> REST/API where required -> ProjectPress UI -> tests`

## Existing integration boundaries

### Storage

Continue using the hybrid storage architecture documented in `PP_Storage.md`:

- custom ProjectPress tables for application entities and relationships;
- WordPress posts/meta for authored content;
- WordPress taxonomies/term meta for classification;
- WordPress attachments for uploaded files;
- WordPress comments for discussion replies;
- WordPress users for canonical user identities.

Do not create a table merely because a domain class exists.

### UI

- Global management belongs under `src/Admin/Manager/ProjectManager/`.
- Per-project workspace features belong under `src/Admin/Manager/Projects/`.
- UI controllers must call domain services/repositories rather than introducing further direct persistence.

### Extension integration

Use the existing project extension/provider infrastructure for workspace sections and related entities rather than adding a second extension mechanism.

## Verified integration gaps

### `src/Admin/Manager/Projects/WorkspaceSections.php`

The current workspace implementation still accesses storage directly through `DBHelper` for milestones, discussions, notes, files, settings and activity. This bypasses the repository/service boundary defined by the storage architecture.

Required follow-up:

1. Replace direct `DBHelper` reads/writes/deletes with owning repositories/services.
2. Preserve the existing nonce and project permission checks.
3. Route authored content through WordPress-backed content services rather than treating every section as a custom-table record.
4. Keep attachment ownership with WordPress and store only ProjectPress relationship metadata.
5. Keep project settings behind an owning settings/service abstraction.

### `src/Includes/Project/Core/ProjectPlan.php`

`ProjectPlan` is currently an in-memory aggregate of a project ID, milestones and work items. It has no persistence responsibility of its own.

Integration decision:

- keep `ProjectPlan` as an aggregate/read model;
- hydrate it from the milestone/work-item persistence layer;
- do not create a `project_plans` table solely for this class;
- expose it through planning/workspace services where a combined project-plan view is required.

### Model-only expansion

The recently expanded domain namespaces under `src/Includes/Project/` must be audited individually before new persistence is introduced:

- Collaboration
- Communication
- Composition
- Delivery
- Governance
- People
- Planning
- Quality
- Reports
- Requirements
- Tracking
- Work
- Workflow

For every class, record one of these states:

- **Aggregate/value object**: no independent storage.
- **Existing entity**: map to an existing ProjectPress table/repository.
- **Authored content**: map to a ProjectPress WordPress post/meta model.
- **Classification**: map to an existing taxonomy/term-meta model.
- **Relationship**: map to an existing relational table or approved new relation.
- **New persistent entity**: requires an explicit schema/repository/service decision before implementation.
- **Presentation/read model**: derive from existing persisted records.

## Implementation order

### Pass 1 - remove persistence leakage from the workspace

Refactor existing workspace sections to use domain-owned services/repositories. This should happen before adding UI for more entities, otherwise the current direct-database pattern will be copied into new sections.

### Pass 2 - planning and work

Connect ProjectPlan, milestones, tasks/work items, roadmap/Gantt presentation and related planning models using existing task/milestone storage wherever possible.

### Pass 3 - people and collaboration

Connect teams/members and determine which collaboration models are WordPress-backed content, attachment relationships, or views over existing records.

### Pass 4 - governance, quality and requirements

Map risks, issues, decisions, dependencies, bugs, test cases and requirements to explicit storage/service contracts. Do not create tables until ownership and lifecycle are defined.

### Pass 5 - composition and delivery

Map assets, attributions, licences, packages, provenance/SBOM, environments, deployments, releases and sprints. Reuse relationships/content metadata when suitable and add schema only for independently queryable application entities.

### Pass 6 - communication, reporting, tracking and workflow

Integrate notifications, reports, activity views and workflow/read models with existing sources rather than duplicating persisted state.

### Pass 7 - API and UI completion

For approved persistent/user-facing entities:

1. provide repository access;
2. provide service validation/workflow;
3. enforce ProjectPress permissions;
4. provide REST routes only where required;
5. register the relevant global/project workspace UI;
6. implement create/edit/view/delete operations appropriate to the entity;
7. add activity/deletion integration where relevant.

### Pass 8 - verification

Add focused tests for each completed integration path and then resume the Phase 8 release-candidate QA checklist.

## Completion rule

A newly introduced model is complete only when either:

1. it is deliberately classified as a non-persistent domain/read/value object and is consumed by an owning service/UI; or
2. its complete persistence -> service -> permission -> API/UI -> test path is implemented.

Placeholder classes with no consumer do not count as integrated functionality.
