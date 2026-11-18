# 3. Authoritative Directory Responsibilities

## 3.1 Project Domain

Project-specific backend functionality belongs under `src/Includes/Project/`.

This area should contain the ProjectPress project domain, including appropriate classes for projects, teams, members, relationships, statuses, priorities, visibility, tasks, milestones, discussions, notes, files or attachments, activity records, project fields, queries, repositories, services, permissions, validation, formatting and hydration.

Do not place admin-page rendering logic or raw schema creation inside the project domain. The project domain may consume persistence services, but database registration and schema ownership must remain within the core database architecture.

## 3.2 Global Project Manager

Global project-management interfaces belong under `src/Admin/Manager/ProjectManager/`.

This area is responsible for creating projects; viewing and filtering projects; managing categories, tags, types, global statuses, priorities, reusable fields, teams and global structures; authorised bulk operations; and installation-wide summaries.

This manager must use ProjectPress services and repositories. It must not access custom tables directly.

## 3.3 Individual Project Interface

Individual project workspace interfaces belong under `src/Admin/Manager/Projects/`.

This area contains project overview, details, members and teams, tasks, milestones, discussions, notes, files, activity, project settings and enabled project-specific modules.

These interfaces must operate within a resolved project context. A project ID, project key or project slug must be validated before loading the workspace, followed by permission checks against the resolved project and current user.

## 3.4 General Settings

General and global ProjectPress settings interfaces belong under `src/Admin/Settings/`.

This area is responsible for general plugin behaviour, default project values, status and priority defaults, date/display settings, permission defaults, feature switches, notification preferences, REST-related configuration, retention/deletion behaviour and extension-related settings.

Settings interfaces must use the existing settings architecture. Do not create a separate ProjectPress settings framework unless the existing capability is genuinely insufficient and explicitly approved.
