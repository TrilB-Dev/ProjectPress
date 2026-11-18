# 11. REST API

REST functionality must extend the repository's existing API architecture.

REST controllers or route services should register routes through the established API system, declare request arguments, validate/sanitise parameters, implement permission callbacks, call ProjectPress services/repositories, return consistent responses, use WordPress error objects for failures and avoid duplicating domain rules already implemented by services.

REST endpoints must not contain schema-management code or raw installation logic.
