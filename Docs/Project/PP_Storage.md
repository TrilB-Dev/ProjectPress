PROJECTPRESS DATA STORAGE ARCHITECTURE
=====================================

You are working inside the ProjectPress WordPress plugin repository.

ProjectPress is an application-style project-management plugin. It uses a
hybrid storage architecture comprising:

1. ProjectPress custom database tables;
2. WordPress custom post types;
3. WordPress post metadata;
4. WordPress custom taxonomies;
5. WordPress term metadata;
6. WordPress attachments;
7. WordPress comments;
8. WordPress users;
9. the existing ProjectPress settings system.

Read all applicable instructions under:

.github/instructions/

before creating or modifying any code.

Inspect the existing database, post type, taxonomy, settings, REST API,
service-registration and extension infrastructure before implementation.

Do not introduce a second database framework, post-type registry, settings
framework or service-container pattern if the repository already provides
one.

Do not write implementation code until the existing repository architecture
has been inspected and the proposed file plan has been reported.

==================================================
1. FUNDAMENTAL STORAGE RULE
==================================================

ProjectPress uses the following ownership rule:

- Custom tables own ProjectPress application entities and relationships.
- WordPress posts own authored project content.
- WordPress post meta extends authored content and links it to ProjectPress
  application entities.
- WordPress taxonomies classify authored project content.
- WordPress term meta configures taxonomy-term presentation and behaviour.
- WordPress attachments own uploaded files.
- WordPress comments provide responses to ProjectPress discussion threads.
- WordPress users remain the canonical user identities.
- The existing ProjectPress settings system owns plugin-wide configuration.

A ProjectPress project is NOT a WordPress post.

The canonical identity of a project is:

{prefix}projects.id

Do not register a project custom post type.

Do not create a corresponding wp_posts record for each project.

Do not create a duplicate project identity in both a custom table and
wp_posts.

==================================================
2. STORAGE RESPONSIBILITIES
==================================================

Use custom database tables for:

- projects;
- project types;
- project members;
- teams;
- team members;
- project/team relationships;
- project field definitions;
- project field values;
- project categories;
- project/category relationships;
- milestones;
- tasks;
- task assignees;
- task relationships;
- project relationships;
- activity records.

Use WordPress posts for:

- project notes;
- project documents;
- authored project updates;
- project discussion threads.

Use WordPress attachments for:

- uploaded project files;
- task attachments;
- milestone attachments;
- note attachments;
- document attachments;
- discussion attachments.

Use WordPress comments for:

- replies to ProjectPress discussion threads.

Use WordPress taxonomies for:

- authored-content categories;
- authored-content tags;
- document types.

Do not use taxonomies for application state such as project status,
visibility, priority, task status or task priority unless the architecture is
explicitly changed later.

==================================================
3. CUSTOM DATABASE TABLE NAMING
==================================================

All custom tables must use the active WordPress database prefix.

Never hard-code:

wp_

Obtain the prefix through the repository's existing database service or
$wpdb->prefix.

The logical ProjectPress table names are:

projects
project_types
project_members

teams
team_members
project_teams

project_fields
project_field_values

project_categories
project_category_relationships

milestones

tasks
task_assignees
task_relationships

project_relationships

activity

For a standard WordPress installation, these might resolve to names similar
to:

wp_projects
wp_project_types
wp_project_members

The actual physical names depend on how the existing
Database::register_core_table() implementation applies prefixes.

Do not assume table names without inspecting that implementation.

==================================================
4. DATABASE REGISTRATION
==================================================

Use the existing ProjectPress database registration architecture:

Database::register_core_table()

Each table method must:

1. register one logical table;
2. return its logical table name;
3. provide its CREATE TABLE statement through the existing callback;
4. use the charset/collation provided by the current database manager;
5. use dbDelta-compatible SQL if the existing manager uses dbDelta();
6. define all columns on separate lines;
7. use PRIMARY KEY with the formatting required by the existing installer;
8. define indexes needed by actual ProjectPress queries;
9. avoid hard-coded table prefixes;
10. avoid direct execution during normal application requests.

The WordPress dbDelta() function is intended for creating and updating table
structures. However, use it only through the repository's existing database
installer or registration layer rather than introducing an unrelated
activation routine.

==================================================
5. PROJECTS TABLE
==================================================

Logical table:

projects

Purpose:

The projects table is the canonical source for ProjectPress projects.

Required columns:

id
project_name
project_slug
project_key
project_description

parent_id
owner_id
type_id

status
priority
visibility

start_date
target_end_date
actual_end_date

created_by
created_at
updated_by
updated_at

Column responsibilities:

id
    Internal canonical project identifier.

project_name
    Human-readable project name.

project_slug
    Stable, URL-safe project identifier used by ProjectPress routing.

project_key
    Short unique project key used in identifiers such as PROJECT-42.

project_description
    Main project description.

parent_id
    Optional reference to another projects.id, establishing a parent/child
    hierarchy.

owner_id
    WordPress user ID of the current project owner.

type_id
    Reference to project_types.id.

status
    Current operational status key.

priority
    Current operational priority key.

visibility
    Project access level.

start_date
    Intended or actual project start date.

target_end_date
    Target project completion date.

actual_end_date
    Actual project completion date.

created_by
    WordPress user ID that created the project.

created_at
    Project creation timestamp.

updated_by
    WordPress user ID that last updated the project.

updated_at
    Project update timestamp.

Recommended status keys:

planning
active
on_hold
completed
cancelled
archived

Recommended priority keys:

low
normal
high
critical

Recommended visibility keys:

private
members
internal
public

These are initial application-level keys, not taxonomy terms.

Required constraints and indexes:

PRIMARY KEY:
    id

UNIQUE:
    project_key
    project_slug

INDEX:
    parent_id
    owner_id
    type_id
    status
    priority
    visibility
    start_date
    target_end_date
    created_by
    updated_at

Do not store these values as serialised columns in projects:

project_members
project_teams
project_fields
project_categories
tasks
milestones
activity

Those values belong in relational tables.

==================================================
6. PROJECT TYPES
==================================================

Logical table:

project_types

Purpose:

Defines reusable project types and optional project-type configuration.

Examples:

General Project
WordPress Plugin
Website
Software Project
Game Mod
Documentation Project
Maintenance Project

Required columns:

id
type_name
type_slug
type_description
type_icon
type_colour
configuration
enabled
created_by
created_at
updated_by
updated_at

The configuration field may store a small JSON-compatible configuration
object for behaviour that is loaded as one unit, such as:

- default workspace sections;
- default project settings;
- default field definitions;
- default status choices;
- project-type presentation.

Do not store members, projects, tasks or other relational records inside the
configuration field.

==================================================
7. PROJECT MEMBERS
==================================================

Logical table:

project_members

Purpose:

Maps WordPress users directly to projects.

Required columns:

id
project_id
user_id
member_role
member_status
permissions
added_by
joined_at
created_at
updated_at

Required unique constraint:

project_id + user_id

Suggested role keys:

owner
manager
member
contributor
reviewer
viewer

Suggested member status keys:

invited
active
suspended
removed

The permissions field may contain project-specific permission overrides.
It must not replace the normal role definition.

Do not serialise the full member collection into the projects table.

==================================================
8. TEAMS AND TEAM MEMBERSHIP
==================================================

Logical tables:

teams
team_members
project_teams

The teams table defines reusable teams.

Required teams columns:

id
team_name
team_slug
team_description
owner_id
status
created_by
created_at
updated_by
updated_at

The team_members table maps WordPress users to teams.

Required team_members columns:

id
team_id
user_id
member_role
member_status
added_by
joined_at
created_at
updated_at

Required team_members unique constraint:

team_id + user_id

The project_teams table allows multiple teams to belong to multiple projects.

Required project_teams columns:

id
project_id
team_id
team_role
assigned_by
assigned_at

Required project_teams unique constraint:

project_id + team_id

Do not store a single project_team value in projects because that would
artificially restrict a project to one team.

==================================================
9. PROJECT CUSTOM FIELDS
==================================================

Logical tables:

project_fields
project_field_values

The project_fields table defines available fields.

Required columns:

id
field_key
field_name
field_description
field_type
configuration
is_required
is_enabled
sort_order
created_by
created_at
updated_by
updated_at

Suggested field types:

text
textarea
integer
decimal
boolean
date
datetime
select
multi_select
user
url
email

The project_field_values table stores values assigned to projects.

Required columns:

id
project_id
field_id
value_text
value_longtext
value_number
value_date
created_at
updated_by
updated_at

Required unique constraint:

project_id + field_id

Use the column appropriate to the field definition.

Do not serialise all custom project fields into one projects.project_fields
column.

Keep queryable numbers and dates in typed value columns.

==================================================
10. PROJECT CATEGORIES
==================================================

Logical tables:

project_categories
project_category_relationships

Project categories are ProjectPress-native entities rather than WordPress
taxonomy terms because ProjectPress projects are not WordPress post objects.

Required project_categories columns:

id
parent_id
category_name
category_slug
category_description
category_colour
category_icon
sort_order
created_by
created_at
updated_at

The parent_id column provides hierarchical categories.

The project_category_relationships table maps projects to categories.

Required columns:

project_id
category_id
assigned_by
assigned_at

Required composite primary or unique key:

project_id + category_id

Do not attempt to insert projects.id into wp_term_relationships as though it
were a WordPress object ID.

==================================================
11. MILESTONES
==================================================

Logical table:

milestones

Required columns:

id
project_id
milestone_name
milestone_description
status
start_date
target_date
completed_at
sort_order
created_by
created_at
updated_by
updated_at

Suggested milestone status values:

open
active
completed
cancelled

Milestones are application entities. They are not WordPress posts or taxonomy
terms.

==================================================
12. TASKS
==================================================

Logical table:

tasks

A task is ProjectPress's structured work-item entity.

Required columns:

id
project_id
milestone_id
parent_id
task_number
task_type
task_name
task_description
status
priority
progress
estimated_minutes
start_date
target_date
completed_at
sort_order
created_by
created_at
updated_by
updated_at

Suggested task type keys:

task
subtask
issue
bug
feature
change
review

Suggested task status keys:

open
in_progress
blocked
in_review
completed
cancelled

Suggested priority keys:

low
normal
high
critical

The task_number is unique within a project.

Required unique constraint:

project_id + task_number

A display identifier may be formed from:

project.project_key + task.task_number

Example:

PRP-42

Do not use that display identifier as the database primary key.

The parent_id column references another tasks.id and supports subtasks.

The milestone_id column references milestones.id.

Tasks must not be stored as posts or post meta in the initial architecture.

==================================================
13. TASK ASSIGNEES
==================================================

Logical table:

task_assignees

Purpose:

Provides a many-to-many relationship between tasks and WordPress users.

Required columns:

task_id
user_id
assigned_by
assigned_at

Required composite key:

task_id + user_id

Do not put a single assigned_user_id column in tasks because ProjectPress
supports multiple assignees.

==================================================
14. TASK RELATIONSHIPS
==================================================

Logical table:

task_relationships

Required columns:

id
source_task_id
target_task_id
relationship
created_by
created_at

Suggested relationship keys:

blocks
blocked_by
depends_on
required_by
relates_to
duplicates
duplicated_by

Required unique constraint:

source_task_id + target_task_id + relationship

The service layer must prevent:

- a task relating to itself;
- duplicate relationship rows;
- invalid reciprocal relationships;
- relationships across inaccessible projects.

==================================================
15. PROJECT RELATIONSHIPS
==================================================

Logical table:

project_relationships

Purpose:

Stores non-hierarchical relationships between projects.

Required columns:

id
source_project_id
target_project_id
relationship
created_by
created_at

Suggested relationships:

depends_on
required_by
blocks
blocked_by
relates_to
supersedes
superseded_by

Use projects.parent_id only for hierarchy.

Do not use parent_id for arbitrary project relationships.

==================================================
16. ACTIVITY
==================================================

Logical table:

activity

Purpose:

Stores structured ProjectPress audit and activity events.

Required columns:

id
project_id
object_type
object_id
action
actor_id
activity_summary
activity_data
created_at

Examples of object_type:

project
task
milestone
member
team
note
document
update
discussion
attachment

Examples of action:

created
updated
deleted
restored
status_changed
priority_changed
assigned
unassigned
commented
uploaded
archived

The activity_data column may contain contextual JSON-compatible details such
as:

{
    "from": "planning",
    "to": "active"
}

Do not place essential filter or relationship values only in activity_data.

Activity records are system events.

They are not the same as authored project updates.

==================================================
17. DATABASE RELATIONSHIPS
==================================================

The primary relationships are:

projects.parent_id
    -> projects.id

projects.owner_id
    -> wp_users.ID

projects.type_id
    -> project_types.id

projects.created_by
    -> wp_users.ID

projects.updated_by
    -> wp_users.ID

project_members.project_id
    -> projects.id

project_members.user_id
    -> wp_users.ID

team_members.team_id
    -> teams.id

team_members.user_id
    -> wp_users.ID

project_teams.project_id
    -> projects.id

project_teams.team_id
    -> teams.id

project_field_values.project_id
    -> projects.id

project_field_values.field_id
    -> project_fields.id

project_category_relationships.project_id
    -> projects.id

project_category_relationships.category_id
    -> project_categories.id

milestones.project_id
    -> projects.id

tasks.project_id
    -> projects.id

tasks.milestone_id
    -> milestones.id

tasks.parent_id
    -> tasks.id

task_assignees.task_id
    -> tasks.id

task_assignees.user_id
    -> wp_users.ID

task_relationships.source_task_id
    -> tasks.id

task_relationships.target_task_id
    -> tasks.id

project_relationships.source_project_id
    -> projects.id

project_relationships.target_project_id
    -> projects.id

activity.project_id
    -> projects.id

ProjectPress should enforce these relationships through repositories and
domain services unless the existing database architecture explicitly uses
SQL foreign-key constraints.

Do not introduce foreign keys without first checking the repository's
existing database conventions and migration strategy.

==================================================
18. WORDPRESS POST TYPES
==================================================

Register these private ProjectPress post types:

pp_note
pp_document
pp_update
pp_discussion

Do not register:

pp_project
pp_task
pp_milestone
pp_team
pp_activity

Post type names must remain within WordPress's post-type key limit.

Register all post types on init.

The ProjectPress post types are private application content types.

Recommended common registration properties:

public
    false

publicly_queryable
    false

exclude_from_search
    true

show_ui
    false

show_in_menu
    false

show_in_admin_bar
    false

show_in_nav_menus
    false

show_in_rest
    true

rewrite
    false

query_var
    false

map_meta_cap
    true

ProjectPress supplies its own administration UI under:

src/Admin/Manager/Projects/

Do not expose the normal WordPress post-management screens unless this design
is explicitly changed later.

==================================================
19. SHARED POST METADATA
==================================================

Register these meta keys for every ProjectPress post type:

_pp_project_id
_pp_visibility
_pp_pinned
_pp_featured
_pp_sort_order

_pp_project_id

Type:
    integer

Purpose:
    References projects.id.

Required:
    yes

Default:
    0 only before validation; valid saved content must reference a real,
    accessible project.

_pp_visibility

Type:
    string

Suggested values:

project
members
private
public

Purpose:
    Controls access to the content record within the owning project.

_pp_pinned

Type:
    boolean

Purpose:
    Pins the content in its relevant ProjectPress interface.

_pp_featured

Type:
    boolean

Purpose:
    Marks the content as featured.

_pp_sort_order

Type:
    integer

Purpose:
    Provides manual ordering within appropriate content views.

Register each meta key with:

- the correct scalar type;
- single set to true;
- sanitisation;
- authorisation;
- show_in_rest where the ProjectPress interface requires REST access.

Do not use a generic current_user_can('edit_posts') check as the final
authorisation model.

The final auth_callback must check:

1. the target ProjectPress project;
2. the current user's project membership or administrative capability;
3. the required action;
4. the content visibility;
5. whether the record belongs to that project.

==================================================
20. PROJECT NOTES
==================================================

Post type:

pp_note

Purpose:

Stores authored notes belonging to a project.

Examples:

- general notes;
- research notes;
- meeting notes;
- development notes;
- planning notes;
- internal notes.

Recommended supports:

title
editor
author
revisions

Note-specific meta:

_pp_note_type
_pp_note_date
_pp_milestone_id
_pp_task_id

_pp_note_type

Type:
    string

Suggested values:

general
meeting
research
development
planning
decision

_pp_note_date

Type:
    string

Purpose:
    Optional semantic date of the note if it differs from post_date.

Store in a consistent date format.

_pp_milestone_id

Type:
    integer

Purpose:
    Optional reference to milestones.id.

_pp_task_id

Type:
    integer

Purpose:
    Optional reference to tasks.id.

When a milestone or task reference is present, validate that it belongs to
the same project as _pp_project_id.

==================================================
21. PROJECT DOCUMENTS
==================================================

Post type:

pp_document

Purpose:

Stores authored, revision-capable project documentation.

Examples:

- project briefs;
- requirements;
- specifications;
- technical documentation;
- user guides;
- developer guides;
- design documents;
- reports;
- proposals;
- release documentation;
- policies.

Recommended supports:

title
editor
excerpt
author
revisions

Document-specific meta:

_pp_document_status
_pp_document_version
_pp_document_reference
_pp_approved_by
_pp_approved_at
_pp_review_date

_pp_document_status

Type:
    string

Suggested values:

draft
review
approved
superseded
archived

This is an application-level document status. It is distinct from the
WordPress post_status.

_pp_document_version

Type:
    string

Examples:

0.1
1.0
2.1

Do not assume semantic versioning unless the document workflow requires it.

_pp_document_reference

Type:
    string

Purpose:
    Human-readable document identifier.

_pp_approved_by

Type:
    integer

Purpose:
    WordPress user ID that approved the document.

_pp_approved_at

Type:
    string

Purpose:
    Approval date/time.

_pp_review_date

Type:
    string

Purpose:
    Date on which the document should next be reviewed.

Document approval must be handled through a domain service rather than
trusting arbitrary submitted meta values.

==================================================
22. PROJECT UPDATES
==================================================

Post type:

pp_update

Purpose:

Stores deliberate, human-authored project updates.

An authored update is not an automatic activity event.

Example authored update:

"Database architecture has been approved and implementation has started."

Example activity event:

"Task 42 status changed from open to in_progress."

Recommended supports:

title
editor
author
revisions

Update-specific meta:

_pp_update_type
_pp_update_date
_pp_progress

_pp_update_type

Type:
    string

Suggested values:

general
progress
milestone
release
risk
announcement

_pp_update_date

Type:
    string

Purpose:
    Semantic update date where needed.

_pp_progress

Type:
    number

Purpose:
    Optional progress value captured when the update was authored.

Validate progress between:

0
and
100

Do not automatically make an authored update authoritative for the project's
current progress unless the domain service explicitly performs that
operation.

==================================================
23. PROJECT DISCUSSIONS
==================================================

Post type:

pp_discussion

Purpose:

Stores a ProjectPress discussion thread.

The post contains:

- title;
- opening message;
- author;
- owning project;
- optional task or milestone context.

WordPress comments provide discussion replies.

Recommended supports:

title
editor
author
comments

Discussion-specific meta:

_pp_discussion_status
_pp_discussion_type
_pp_milestone_id
_pp_task_id
_pp_resolved_by
_pp_resolved_at

_pp_discussion_status

Type:
    string

Suggested values:

open
resolved
closed

_pp_discussion_type

Type:
    string

Suggested values:

general
question
decision
proposal
issue
review

_pp_resolved_by

Type:
    integer

Purpose:
    WordPress user ID that resolved the discussion.

_pp_resolved_at

Type:
    string

Purpose:
    Resolution date/time.

A resolved discussion may be reopened if permissions allow.

Changing discussion status must pass through the appropriate service rather
than allowing unrestricted meta updates.

==================================================
24. ATTACHMENTS
==================================================

Use the built-in WordPress attachment post type.

Do not register a separate pp_file post type.

Register these ProjectPress attachment meta fields:

_pp_project_id
_pp_object_type
_pp_object_id
_pp_file_visibility
_pp_file_description

_pp_project_id

Type:
    integer

Purpose:
    References projects.id.

_pp_object_type

Type:
    string

Suggested values:

project
task
milestone
note
document
update
discussion
comment

_pp_object_id

Type:
    integer

Purpose:
    Identifies the associated ProjectPress or WordPress object.

Interpretation depends on _pp_object_type.

Examples:

object_type = task
object_id   = tasks.id

object_type = milestone
object_id   = milestones.id

object_type = note
object_id   = wp_posts.ID for a pp_note record

object_type = document
object_id   = wp_posts.ID for a pp_document record

_pp_file_visibility

Type:
    string

Suggested values:

project
members
private
public

_pp_file_description

Type:
    string

Purpose:
    Optional ProjectPress-specific file description.

Validate that:

1. the referenced project exists;
2. the current user can upload to the project;
3. the target object exists;
4. the target object belongs to the same project;
5. the object-type/object-ID pair is valid.

Do not store file paths directly when WordPress already owns the attachment
and its file metadata.

==================================================
25. WORDPRESS COMMENTS
==================================================

Use WordPress comments for replies to pp_discussion posts.

Do not create a custom discussion-replies table initially.

A valid ProjectPress discussion comment must:

1. reference a post with post_type = pp_discussion;
2. inherit its project association from _pp_project_id;
3. pass ProjectPress project-access checks;
4. respect the discussion status;
5. respect the user's discussion permissions;
6. use WordPress comment sanitisation and moderation where appropriate.

If discussion comments need ProjectPress-specific metadata later, register
comment meta explicitly rather than placing arbitrary unregistered values in
wp_commentmeta.

Potential later comment metadata:

_pp_edited_by
_pp_edited_at
_pp_marked_answer
_pp_resolution_reference

Do not implement these until required.

==================================================
26. TAXONOMIES
==================================================

Register these ProjectPress taxonomies:

pp_content_category
pp_content_tag
pp_document_type

Register taxonomies on init.

Register the taxonomy associations explicitly with the corresponding post
types.

The taxonomy keys must remain within the WordPress taxonomy-key limit.

==================================================
27. CONTENT CATEGORY
==================================================

Taxonomy:

pp_content_category

Attached to:

pp_note
pp_document
pp_update
pp_discussion

Hierarchical:

true

Purpose:

Provides reusable hierarchical classification for authored ProjectPress
content.

Examples:

Development
    Backend
    Frontend
    Database
    REST API

Documentation
    Developer
    User

Design
    UI
    UX

Recommended registration:

public
    false

publicly_queryable
    false

show_ui
    false

show_in_menu
    false

show_in_rest
    true

rewrite
    false

query_var
    false

ProjectPress provides its own category-selection and category-management UI.

==================================================
28. CONTENT TAGS
==================================================

Taxonomy:

pp_content_tag

Attached to:

pp_note
pp_document
pp_update
pp_discussion

Hierarchical:

false

Purpose:

Provides flexible labels for authored project content.

Examples:

wordpress
php
database
rest-api
security
planning
release
design
testing

Use content tags for discoverability and filtering.

Do not use them for project permissions, project status or operational task
state.

==================================================
29. DOCUMENT TYPES
==================================================

Taxonomy:

pp_document_type

Attached to:

pp_document

Hierarchical:

true

Purpose:

Classifies ProjectPress documents independently of general content
categories.

Suggested terms:

Project Brief
Requirements
Specification
Technical
Design
User Guide
Developer Guide
Research
Proposal
Report
Release
Policy

Document type is a taxonomy because it is a reusable classification assigned
to authored documents.

Document status remains post meta because it is mutable workflow state rather
than general classification.

==================================================
30. TERM METADATA
==================================================

Register these term meta keys for all ProjectPress taxonomies:

_pp_colour
_pp_icon
_pp_sort_order
_pp_enabled

_pp_colour

Type:
    string

Purpose:
    Optional term display colour.

Sanitise as a valid hexadecimal colour.

_pp_icon

Type:
    string

Purpose:
    Optional approved icon key.

Do not allow arbitrary unescaped HTML.

_pp_sort_order

Type:
    integer

Default:
    0

Purpose:
    Controls display order in ProjectPress interfaces.

_pp_enabled

Type:
    boolean

Default:
    true

Purpose:
    Allows a term to be disabled without deleting historical relationships.

Register term metadata with register_term_meta() using the specific taxonomy
as the object subtype.

==================================================
31. WHY THERE IS NO CONTENT-TYPE TAXONOMY
==================================================

Do not register:

pp_content_type

Do not store:

_pp_content_type

The post type already defines the content type:

pp_note
    note

pp_document
    document

pp_update
    update

pp_discussion
    discussion

Adding a content-type taxonomy or meta key would duplicate the post_type
column and introduce possible inconsistencies.

==================================================
32. WORDPRESS USER OWNERSHIP
==================================================

WordPress users remain the canonical user records.

Use wp_users.ID for:

projects.owner_id
projects.created_by
projects.updated_by

project_members.user_id
project_members.added_by

teams.owner_id
teams.created_by
teams.updated_by

team_members.user_id
team_members.added_by

project_teams.assigned_by

project_fields.created_by
project_fields.updated_by

project_field_values.updated_by

milestones.created_by
milestones.updated_by

tasks.created_by
tasks.updated_by

task_assignees.user_id
task_assignees.assigned_by

task_relationships.created_by

project_relationships.created_by

activity.actor_id

WordPress post authorship continues to use:

wp_posts.post_author

Do not duplicate complete WordPress user profiles in ProjectPress tables.

ProjectPress may add project-specific roles and permission overrides without
replacing WordPress authentication.

==================================================
33. QUERY OWNERSHIP
==================================================

All custom-table queries must be implemented through repository classes.

Admin pages, REST controllers, AJAX handlers and view templates must not
contain direct SQL.

Expected repositories include:

ProjectRepository
ProjectTypeRepository
ProjectMemberRepository
TeamRepository
ProjectFieldRepository
ProjectCategoryRepository
MilestoneRepository
TaskRepository
ActivityRepository

Expected WordPress-content repositories or services include:

NoteRepository
DocumentRepository
UpdateRepository
DiscussionRepository
AttachmentRepository

Repository classes are responsible for persistence operations.

Service classes are responsible for:

- validation;
- workflows;
- permissions;
- cross-entity consistency;
- transactions where appropriate;
- activity logging;
- deletion and restoration rules.

==================================================
34. WRITE OPERATIONS
==================================================

All writes must use the appropriate WordPress database API or existing
ProjectPress database abstraction.

Use:

$wpdb->insert()
$wpdb->update()
$wpdb->delete()
$wpdb->prepare()

or equivalent safe methods supplied by the repository.

Never concatenate untrusted values into SQL.

For posts, use WordPress APIs such as:

wp_insert_post()
wp_update_post()
wp_delete_post()

For post meta, use:

get_post_meta()
update_post_meta()
delete_post_meta()

For taxonomies, use:

wp_set_object_terms()
wp_get_object_terms()
wp_remove_object_terms()

For term meta, use:

get_term_meta()
update_term_meta()
delete_term_meta()

For comments, use the applicable WordPress comment APIs.

Do not bypass WordPress hooks and cache invalidation by writing directly to
wp_posts, wp_postmeta, wp_terms, wp_termmeta, wp_term_relationships,
wp_comments or wp_commentmeta.

==================================================
35. DATE AND TIME STORAGE
==================================================

Use a consistent date/time strategy.

For custom database records:

- store exact event timestamps in UTC where practical;
- convert for display using WordPress date/time functions;
- use nullable values for dates that are genuinely unknown;
- avoid invalid zero-date placeholders;
- distinguish date-only values from exact timestamps.

For WordPress posts:

- retain WordPress's normal post_date and post_date_gmt handling;
- use registered metadata only when a separate semantic date is required.

Do not use the server's implicit timezone as an undocumented storage rule.

==================================================
36. JSON AND LONGTEXT FIELDS
==================================================

The following fields may contain structured JSON-compatible data:

project_types.configuration
project_members.permissions
project_fields.configuration
activity.activity_data

Rules:

1. Use them only for supplementary configuration or context.
2. Do not put relationships in JSON.
3. Do not put values needed for primary filters only in JSON.
4. Encode and decode centrally.
5. Validate decoded structures.
6. Return safe defaults when data is absent or invalid.
7. Document the schema for each JSON field.
8. Add a data-version key if a structure is expected to evolve materially.

Do not use PHP serialisation for new ProjectPress application data unless the
existing framework mandates it and compatibility requires it.

==================================================
37. INDEXING RULES
==================================================

Create indexes based on actual ProjectPress access patterns.

Expected common queries include:

- project by ID;
- project by project key;
- project by slug;
- child projects by parent ID;
- projects by owner;
- projects by type;
- projects by status;
- projects by priority;
- projects visible to a user;
- projects assigned to a member;
- teams assigned to a project;
- field values for a project;
- projects in a category;
- milestones for a project;
- tasks for a project;
- tasks by project and status;
- tasks by project and priority;
- tasks assigned to a user;
- activity by project and creation date;
- authored posts by _pp_project_id and post type.

Do not add speculative indexes blindly.

For WordPress content, be aware that _pp_project_id lives in wp_postmeta.
Centralise ProjectPress content queries so the storage strategy can later be
optimised without rewriting the admin interface.

==================================================
38. SECURITY AND PERMISSIONS
==================================================

Every operation must validate both WordPress capability and ProjectPress
project access.

Do not rely solely on:

current_user_can('edit_posts')

ProjectPress must distinguish actions such as:

project.view
project.edit
project.delete

members.view
members.manage

teams.view
teams.assign

fields.view
fields.manage

milestones.view
milestones.create
milestones.edit
milestones.delete

tasks.view
tasks.create
tasks.edit
tasks.delete
tasks.assign

notes.view
notes.create
notes.edit
notes.delete

documents.view
documents.create
documents.edit
documents.approve
documents.delete

updates.view
updates.create
updates.edit
updates.delete

discussions.view
discussions.create
discussions.reply
discussions.resolve
discussions.close

files.view
files.upload
files.delete

Permissions should consider:

- site-level WordPress capabilities;
- ProjectPress administrative capabilities;
- project ownership;
- direct project membership;
- team membership;
- project role;
- per-member permission overrides;
- project visibility;
- content visibility;
- object ownership where applicable.

REST permission callbacks must perform the same checks as admin actions.

Do not use __return_true for protected routes.

==================================================
39. VALIDATION RULES
==================================================

Before saving a custom-table relationship:

- verify the source record exists;
- verify the target record exists;
- verify both records belong to compatible scopes;
- verify the current user may create the relationship;
- prevent duplicate relationships;
- prevent prohibited self-references.

Before saving ProjectPress post meta:

- verify _pp_project_id exists;
- verify the current user can access the project;
- verify task and milestone references belong to that project;
- verify enum-like values against allowed keys;
- validate user IDs;
- validate dates;
- validate progress ranges;
- reject unsupported object types.

Before assigning taxonomy terms:

- verify the taxonomy is registered for that post type;
- verify the user may assign terms;
- verify disabled terms cannot be newly assigned unless explicitly allowed;
- preserve existing historical assignments when disabling a term.

==================================================
40. DELETION AND RESTORATION
==================================================

Do not implement destructive deletion behaviour implicitly.

Project deletion should support a deliberate lifecycle.

Recommended initial behaviour:

1. Archive a project through status = archived.
2. Hide archived projects from default active views.
3. Preserve members, tasks, milestones, activity and authored content.
4. Require a separate privileged operation for permanent deletion.

Before implementing permanent deletion, define what happens to:

- child projects;
- project members;
- project/team assignments;
- custom field values;
- category relationships;
- milestones;
- tasks;
- task assignees;
- task relationships;
- project relationships;
- activity records;
- notes;
- documents;
- updates;
- discussions;
- comments;
- attachments.

Recommended permanent-deletion rule:

- delete project-owned relational rows;
- delete or trash project-owned authored posts according to approved policy;
- remove ProjectPress relationships from attachments;
- do not delete a physical attachment if it is still referenced elsewhere;
- preserve an administrative audit record if legally and technically
  appropriate;
- execute deletion through a service;
- do not place cascading deletion logic in an admin page controller.

Restoration must revalidate parent, type, owner, team and user references.

==================================================
41. SETTINGS STORAGE
==================================================

Use the repository's existing settings system for plugin-wide options.

Suggested settings groups:

projectpress_general
projectpress_projects
projectpress_permissions
projectpress_tasks
projectpress_content
projectpress_notifications
projectpress_integrations

Examples of global settings:

- default project status;
- default project priority;
- default project visibility;
- project-key format;
- enabled project modules;
- default task status;
- document approval requirement;
- file-upload rules;
- retention behaviour.

Do not place global ProjectPress configuration in the projects table.

Do not introduce a new custom settings table unless the existing settings
infrastructure requires one.

==================================================
42. CACHE AND INVALIDATION
==================================================

Custom-table records do not automatically receive all behaviours of
WordPress posts.

If caching is introduced:

- centralise cache keys;
- include the project or object ID;
- invalidate on writes;
- invalidate relationship collections;
- avoid stale project permission results;
- do not cache access decisions across users incorrectly.

Potential cache groups:

projectpress_projects
projectpress_project_members
projectpress_teams
projectpress_milestones
projectpress_tasks
projectpress_activity

Do not add caching before repositories and mutation boundaries are stable.

==================================================
43. DATA MIGRATIONS
==================================================

Maintain a ProjectPress database schema version.

The installer or migration service must:

1. compare the installed schema version with the plugin schema version;
2. register all current table definitions;
3. run required migrations in order;
4. migrate data before removing obsolete columns;
5. update the schema version only after successful completion;
6. make migrations safe to run more than once where practical;
7. log or return migration failures;
8. avoid running expensive migrations on every request.

Do not depend exclusively on plugin activation.

Plugin files can be updated while the plugin remains active, so schema checks
must fit the repository's existing update lifecycle.

Never rename or remove a populated column without an explicit migration.

==================================================
44. MULTISITE
==================================================

Before implementing installation, inspect whether the existing database
manager registers per-site or network-global tables.

Default recommendation:

- ProjectPress project tables are site-specific;
- use the current site's table prefix;
- do not assume the main site's prefix;
- switch table resolution correctly when operating in another site context;
- do not create network-global ProjectPress tables unless specifically
  required.

The implementation must follow the existing repository's multisite policy.

==================================================
45. REPOSITORY STRUCTURE
==================================================

Keep ProjectPress domain storage under:

src/Includes/Project/

Recommended structure:

src/Includes/Project/
├── Database/
│   ├── Tables.php
│   ├── Schema.php
│   ├── Installer.php
│   └── Migrator.php
│
├── Entity/
│   ├── Project.php
│   ├── ProjectType.php
│   ├── ProjectMember.php
│   ├── Team.php
│   ├── ProjectField.php
│   ├── ProjectCategory.php
│   ├── Milestone.php
│   ├── Task.php
│   └── Activity.php
│
├── Repository/
│   ├── ProjectRepositoryInterface.php
│   ├── ProjectRepository.php
│   ├── ProjectTypeRepository.php
│   ├── ProjectMemberRepository.php
│   ├── TeamRepository.php
│   ├── ProjectFieldRepository.php
│   ├── ProjectCategoryRepository.php
│   ├── MilestoneRepository.php
│   ├── TaskRepository.php
│   └── ActivityRepository.php
│
├── Service/
│   ├── ProjectService.php
│   ├── MembershipService.php
│   ├── TeamService.php
│   ├── ProjectFieldService.php
│   ├── ProjectCategoryService.php
│   ├── MilestoneService.php
│   ├── TaskService.php
│   ├── ActivityService.php
│   └── ProjectDeletionService.php
│
├── Content/
│   ├── PostTypes.php
│   ├── PostMeta.php
│   ├── Taxonomies.php
│   ├── TermMeta.php
│   ├── Attachments.php
│   └── Comments.php
│
├── Permission/
│   ├── ProjectCapabilities.php
│   ├── ProjectPermissions.php
│   └── ContentPermissions.php
│
├── Validation/
│   ├── ProjectValidator.php
│   ├── MembershipValidator.php
│   ├── MilestoneValidator.php
│   ├── TaskValidator.php
│   └── ContentValidator.php
│
└── ValueObject/
    ├── ProjectKey.php
    ├── ProjectStatus.php
    ├── ProjectPriority.php
    ├── ProjectVisibility.php
    ├── TaskStatus.php
    └── TaskPriority.php

Adapt this to the repository's existing naming and service-registration
conventions.

Do not create this entire tree blindly.

First map each responsibility to the closest existing repository component,
then report which files should be created or reused.

==================================================
46. ADMIN BOUNDARIES
==================================================

The global ProjectPress management interface belongs under:

src/Admin/Manager/ProjectManager/

It handles interfaces such as:

- all projects;
- create project;
- project types;
- project categories;
- teams;
- global fields;
- global ProjectPress management.

The per-project workspace belongs under:

src/Admin/Manager/Projects/

It handles interfaces such as:

- project overview;
- details;
- members;
- teams;
- milestones;
- tasks;
- notes;
- documents;
- updates;
- discussions;
- files;
- activity;
- project-specific settings.

Global settings belong under:

src/Admin/Settings/

Admin managers must call domain services.

They must not run SQL directly.

They must not contain persistence logic.

==================================================
47. REST API BOUNDARIES
==================================================

Use the repository's existing REST API infrastructure.

Custom-table entities may require ProjectPress-specific controllers and
routes.

WordPress-backed content may use either:

- WordPress's registered post/meta/taxonomy REST support; or
- ProjectPress-specific REST façades.

Choose consistently after inspecting the current API architecture.

Do not expose internal table structures directly as public API contracts.

REST responses should use schemas or response objects so internal storage can
change without unnecessarily breaking clients.

Every protected route requires:

- authentication;
- permission callback;
- input schema;
- sanitisation;
- validation;
- structured errors;
- consistent response formatting.

==================================================
48. EXTENSIBILITY
==================================================

The repository includes an extension model. Use it rather than creating a
competing extension loader.

Potential future extension points include:

- registering project workspace sections;
- registering project field types;
- registering project types;
- registering task types;
- registering status definitions;
- registering activity event handlers;
- registering content metadata;
- registering taxonomies;
- registering REST routes;
- registering settings groups.

Do not allow extensions to write arbitrary unvalidated data directly into
core tables.

Provide stable service or repository contracts where extension access is
required.

==================================================
49. TESTING REQUIREMENTS
==================================================

Add tests for:

- project creation;
- project-key and slug uniqueness;
- parent-project validation;
- project membership uniqueness;
- team membership;
- project/team relationships;
- project custom field values;
- category relationships;
- milestone ownership;
- task numbering;
- task hierarchy;
- task assignees;
- task relationships;
- activity creation;
- post-to-project relationships;
- task and milestone post-meta validation;
- taxonomy assignment;
- term metadata;
- discussion/comment permissions;
- attachment ownership;
- deletion and restoration;
- permission boundaries;
- database migrations.

Use only the testing commands actually configured in the repository.

Do not claim a check passed unless it was run successfully.

==================================================
50. IMPLEMENTATION ORDER
==================================================

Implement the storage architecture in phases.

Phase 0: Audit

- Read .github/instructions/.
- Inspect existing database registration.
- Inspect settings registration.
- Inspect service registration.
- Inspect post type and taxonomy examples.
- Inspect the REST framework.
- Inspect activation/update handling.
- Inspect tests.
- Do not modify code.

Phase 1: Core project schema

- projects;
- project_types;
- project_members;
- repositories;
- validators;
- project permission foundation.

Phase 2: Team and classification schema

- teams;
- team_members;
- project_teams;
- project_categories;
- project category relationships.

Phase 3: Project planning schema

- milestones;
- tasks;
- task assignees;
- task relationships;
- project relationships.

Phase 4: Fields and activity

- project_fields;
- project_field_values;
- activity.

Phase 5: WordPress content layer

- pp_note;
- pp_document;
- pp_update;
- pp_discussion;
- shared post metadata;
- type-specific post metadata.

Phase 6: Taxonomy layer

- pp_content_category;
- pp_content_tag;
- pp_document_type;
- shared term metadata.

Phase 7: Attachments and comments

- attachment relationship metadata;
- discussion comment integration;
- access validation.

Phase 8: REST, documentation and tests

- API integration;
- migration coverage;
- repository tests;
- permission tests;
- storage documentation.

At the beginning of every implementation phase:

1. list files to create;
2. list files to modify;
3. explain each file's responsibility;
4. identify compatibility risks;
5. identify migration risks;
6. wait for approval before broad changes.

==================================================
51. FIRST RESPONSE REQUIRED
==================================================

For the first response, do not generate implementation code.

Inspect the repository and return:

1. Existing Database Infrastructure
2. Existing Table Registration Lifecycle
3. Existing Post Type Infrastructure
4. Existing Post Meta Infrastructure
5. Existing Taxonomy Infrastructure
6. Existing Term Meta Infrastructure
7. Existing REST API Integration
8. Existing Service Registration
9. Existing Permission Infrastructure
10. Existing Test Infrastructure
11. Proposed File Mapping
12. Proposed Service Mapping
13. Conflicts With Existing Architecture
14. Missing Architectural Decisions
15. Phase 1 Implementation Plan
16. Files That Would Be Created
17. Files That Would Be Modified
18. Validation and Security Risks
19. Migration Risks
20. Questions Requiring Approval

Every statement about the existing repository must include the file path from
which it was verified.

Clearly distinguish:

- verified repository facts;
- recommendations;
- decisions requiring approval.

Do not invent existing classes, methods, files or hooks.

Do not begin implementation until the audit is reviewed.