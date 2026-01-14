# Infrastructure

## Purpose
This directory contains **technical implementations**:
persistence, integrations, and framework plumbing.

## Why this layer exists
It provides concrete implementations for domain needs:
- Database access (Doctrine)
- Mailing (Mailer)
- Framework events/listeners
- Security integrations

## What belongs here
- **Repository/**  
  Doctrine repositories (queries, QueryBuilder, persistence)

- **Mailer/**  
  Email transport, adapters, template wiring

- **EventListener/**  
  Symfony listeners and subscribers

- **Security/**  
  Technical security adapters and implementations

## What does NOT belong here
- Core business rules (Domain)
- HTTP request/response handling (Http)
- Generic helpers that don't depend on infrastructure (Support)

## Dependency rules
- Infrastructure may depend on Domain
- Domain must never depend on Infrastructure

## Notes
If it touches:
- the database
- the filesystem
- an external API
- Symfony internals

it typically belongs here.
