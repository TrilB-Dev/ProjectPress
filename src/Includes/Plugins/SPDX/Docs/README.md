# ProjectPress SPDX

ProjectPress SPDX is an internal ProjectPress extension for Software Bill of Materials support using SPDX.

## Architecture

The plugin follows the same internal-plugin boundary as the bundled GitHub integration. SPDX-specific generation and UI remain inside this plugin rather than being coupled to ProjectPress core.

## Initial capability

- Registers as `projectpress-spdx`.
- Adds an `SBOM` project workspace section through the existing workspace extension filter.
- Generates a minimal SPDX 2.3 JSON document for the current project.
- Keeps the generator isolated so ProjectPress composition records can be mapped into packages, files and relationships in later iterations.

## Next integration work

1. Map ProjectPress Component, Package, Licence, Relationship, Attribution and Provenance models into an SPDX-neutral composition service.
2. Add plugin-owned persistence for SPDX-specific identifiers, checksums, external references and generated document history where required.
3. Add versioned SBOM export attachments.
4. Add project permission checks for generation/export.
5. Add local validation and tests.
6. Add optional external validation/conversion integration without making external services mandatory.
