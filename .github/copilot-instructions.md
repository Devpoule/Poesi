# Copilot instructions for this repository

Summary
- This is a Symfony-based PHP API (see `api/`). Domain-driven layout: `src/Domain`, `src/Http`, `src/Infrastructure`.

Quick commands
- Install dependencies: `cd api && composer install`
- Start locally (Docker): `docker compose -f api/compose.yaml up --build`
- Run console: `cd api && php bin/console` (entrypoint for migrations, fixtures, clearing cache)
- Run migrations: `cd api && php bin/console doctrine:migrations:migrate`
- Serve (simple PHP): `cd api && php -S 127.0.0.1:8000 -t public`

Architecture notes (what matters for code changes)
- DDD layout: core domain models live in `api/src/Domain/Entity` and `api/src/Domain/Repository`.
- HTTP layer: controllers are in `api/src/Http/Controller` and map requests to services/responses.
- Infrastructure: persistence and framework integrations in `api/src/Infrastructure/Repository` and `api/src/Infrastructure/Security`.
- Configuration: Symfony config files in `api/config/packages/*.yaml` (security, doctrine, lexik_jwt_authentication).
- Entry points: `api/public/index.php` and `api/bin/console`.

Patterns & conventions (codebase-specific)
- Use services injected via constructor; prefer typed arguments (PHP 8+). Look at controllers for examples.
- Repositories implement domain repository interfaces under `src/Domain/Repository` and concrete classes live in `src/Infrastructure/Repository`.
- DTOs/Requests/Responses: request validation objects are in `api/src/Http/Request` and responses in `api/src/Http/Response`.
- Exceptions: project defines domain and HTTP exceptions under `api/src/Domain/Exception` and `api/src/Http/Exception`.
- Seeding: initial data JSON lives in `api/resources/lore/*.initial.json` — used by bootstrap/fixtures.

Security & integrations
- JWT authentication via Lexik bundle. Keys and config in `api/config/packages/lexik_jwt_authentication.yaml` and `api/config/`.
- Watch for `security.yaml` and firewall rules; many endpoints require JWT tokens.
- External integrations (email, third-party APIs) live in `api/src/Infrastructure/Mailer` and similar folders.

Files to inspect first when making changes
- `api/src/Kernel.php` — app bootstrap
- `api/config/packages/*.yaml` — security, doctrine, framework
- `api/src/Domain` and `api/src/Infrastructure` — domain model and persistence
- `api/public/index.php` and `api/bin/console` — run/debug entrypoints

Guidance for code modifications
- Prefer changing/adding domain interfaces first, then implement in `Infrastructure` and adjust DI services in `config/services.yaml`.
- When adding new endpoints, create Request DTO in `Http/Request`, Controller method in `Http/Controller`, and Response in `Http/Response`.
- Run migrations after schema changes: `php bin/console doctrine:migrations:diff` then `migrate`.

Testing & CI
- No dedicated tests present in repository root; if adding tests, follow PHPUnit + PSR-12 conventions and run from `api/`.

Examples (copyable snippets)
- Create migration:
```
cd api
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```
- Run API with Docker Compose:
```
docker compose -f api/compose.yaml up --build
```

If unsure
- Inspect `api/config/packages/*.yaml` and `api/src/Kernel.php` to understand service wiring and environment-specific config.
- Search for examples in `api/src/*` when adding a similar feature.

If you want changes
- Tell me which sections to expand or show example edits (controller, repository, config). I can update this file.
