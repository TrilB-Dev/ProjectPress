# 14. Extension Compatibility

ProjectPress must preserve the existing internal and external extension model.

Core services should expose documented actions and filters around important lifecycle operations, including project creation/update/deletion or archival, membership changes, status/priority changes, task/milestone lifecycle events, project interface registration and REST response preparation.

Extensions must not be required to modify core files directly. Extension-defined table requirements must integrate with the approved database-registration architecture rather than running uncontrolled installation SQL.
