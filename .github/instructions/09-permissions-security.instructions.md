# 9. Permissions and Security

Every state-changing operation must include authentication where appropriate, capability checks, project membership/role checks where applicable, nonce verification for browser requests, input sanitisation, allowed-value validation, output escaping, prepared SQL for dynamic queries and structured error handling.

Global administration capabilities and project-level permissions must remain distinct. Access to one project must not automatically grant access to unrelated projects.

Never trust project IDs, user IDs, client-supplied status/priority values, REST fields, AJAX data, user-supplied URLs/filenames or stored values when output escaping is required.
