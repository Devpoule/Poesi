# Command

## Purpose
This directory contains **application commands** executed via CLI.

Commands are used for:
- data synchronization
- maintenance tasks
- one-shot or repeatable operations
- controlled side effects outside HTTP flows

## Typical use cases
- Synchronizing canonical data from JSON (Lore)
- Database maintenance or cleanup
- Batch operations
- Debug or inspection tools

## What belongs here
- Symfony Console commands (`AsCommand`)
- Application-level orchestration logic
- Controlled interaction with:
  - Domain services
  - Infrastructure repositories

## What does NOT belong here
- Business rules (Domain)
- HTTP logic (Controllers, Requests, Responses)
- Raw Doctrine queries (delegate to Infrastructure repositories)

## Design rules
- Commands may coordinate multiple services
- Commands may read files (JSON, CSV, etc.)
- Commands must remain deterministic and explicit
- Prefer `--dry-run`, `--insert-only`, `--purge` options when mutating data

## Notes
Commands act as **fixtures++**:
repeatable, explicit, versionable, and safe to re-run.

## Useful commands
- `app:seed-all`: run migrations, sync lore, and seed users.
- `app:reset-db`: drop/create database, run migrations, then seed (via `app:seed-all --no-migrate`).
  - `--no-seed`: skip seeding after migrations.
  - `--insert-only`: insert-only mode for seeding (no updates).
