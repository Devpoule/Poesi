# Infrastructure

## Purpose
This directory contains technical implementations: persistence, integrations and framework plumbing.

## Why this layer exists
It provides concrete implementations for domain/application needs:
- Database access (Doctrine)
- Mailing (Mailer)
- Framework events/listeners
- Security integrations

## What belongs here
- **Repository/**: Doctrine repositories (queries, QueryBuilder, persistence)
- **Mailer/**: email transport, templates wiring, adapters
- **EventListener/**: Symfony listeners/subscribers
- **Security/**: technical security adapters and implementations

## What does NOT belong here
- Core business rules (Domain)
- HTTP request/response handling (Http)
- Generic helpers that don’t depend on infrastructure (Support)

## Dependency rules
- Infrastructure may depend on Domain (to implement contracts / persist entities).
- Domain must not depend on Infrastructure.

## Notes
If it touches the database, the filesystem, an external API, or Symfony internals:
it typically belongs here.
