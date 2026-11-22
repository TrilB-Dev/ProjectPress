# ProjectPress

ProjectPress is a modular WordPress project-management plugin by TrilB.Dev. It provides global project management, individual project workspaces, project-owned data, REST API support, and an extension/integration layer.

## Requirements

- WordPress with REST API support
- PHP compatible with the Composer dependencies in this repository
- Composer for PHP development dependencies
- Node.js and npm when rebuilding frontend assets

Install PHP dependencies with Composer and build frontend assets with:

```bash
composer install
npm install
npm run build
```

Compiled frontend assets are written beneath `src/Assets/dist`.

## Architecture

ProjectPress follows a hybrid storage model. Project backend/domain code lives under `src/Includes/Project/`; global project-management interfaces live under `src/Admin/Manager/ProjectManager/`; individual project workspace interfaces live under `src/Admin/Manager/Projects/`; and REST API code lives under `src/API/`.

Shared helpers are provided under `src/Includes/Functions/Helpers/` and should be reused rather than duplicated in domain, admin, or API code.

The core project workspace covers overview, project details, members, tasks, milestones, discussions, notes, files, activity, and project-specific settings. Extension registration points support internal and separately installed WordPress integrations.

## REST API

The versioned ProjectPress REST namespace is `projectpress/v1`. Route implementations, permission callbacks, and related API components are maintained under `src/API/`.

See [REST API](Docs/API.md) for additional API documentation.

## Testing and QA

PHPUnit is configured in `phpunit.xml.dist` with `Test/bootstrap.php` and tests under `Test/Unit/`.

Run the principal checks with:

```bash
composer test
composer lint
npm run build
```

The repository also includes a GitHub Actions QA workflow that runs PHP syntax checks, PHPUnit, and coding standards for pushes and pull requests targeting `Master`.

## Documentation

- [REST API](Docs/API.md)
- [Helpers](Docs/HELPERS.md)
- [Settings](Docs/SETTINGS.md)
- [Internal plugins](Docs/INTERNAL_PLUGINS.md)
- [WordPress plugin integration](Docs/WORDPRESS_PLUGINS.md)

## Project Structure

```text
src/
|- API/                         REST API routes and API components
|- Admin/                       Global management and project workspace UI
|- Assets/                      Shared source and compiled assets
|- Includes/Core/               Core database, schema, defaults and services
|- Includes/Functions/Helpers/  Reusable helper functionality
|- Includes/Project/            Project domain and persistence components
|- Includes/Plugins/            Internal plugin infrastructure
|- Includes/Settings/           Settings infrastructure
|- Languages/                   Translation resources
`- Plugin.php                   Main plugin bootstrap

Test/
|- Unit/                        PHPUnit unit tests
`- bootstrap.php                Unit-test bootstrap
```

## Development Rules

The authoritative development rules live under `.github/instructions/`. Changes should follow those architecture, storage, security, implementation and validation requirements.

Before completing a change, run the applicable automated checks and review input sanitisation, output escaping, permissions, nonces, prepared SQL, REST permission callbacks, and reuse of existing helpers.

## License

ProjectPress is licensed under GPL-2.0-or-later, as declared by `composer.json`.
