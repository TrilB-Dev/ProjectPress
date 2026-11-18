# 7. Domain and Persistence Separation

ProjectPress should separate storage operations from business rules.

## 7.1 Repository Responsibilities

Repositories read, insert, update, delete/archive and query records through approved storage, return predictable domain data and handle storage-level conversions. They must not render interfaces or process raw HTTP requests.

## 7.2 Service Responsibilities

Services apply business rules, validate cross-entity operations, coordinate repositories, enforce lifecycle behaviour, coordinate related changes, trigger actions/events and return structured results or WordPress errors. They must not contain admin-page markup.

## 7.3 Model or Entity Responsibilities

Models/entities represent ProjectPress domain records, expose typed or consistently normalised values and may provide domain-level convenience behaviour. They must avoid direct request handling, screen output and independent database connections.

## 7.4 Controller and Manager Responsibilities

Controllers/managers receive requests, verify nonces where required, sanitise input, check permissions, call services, handle responses and select views/responses. They must not contain raw SQL.
