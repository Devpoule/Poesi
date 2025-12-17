# Domain

## Purpose
This directory contains the core business model of the application.

## Why this layer exists
The Domain is the most stable part of the system. It describes *what the app is*
and *what rules must always hold*, regardless of delivery (HTTP) or persistence (DB).

## What belongs here
- **Entity/**: domain entities with business meaning (state + invariants)
- **Enum/**: domain enums (moods, rarities, statuses, etc.)
- **Exception/**: domain errors (invalid state, forbidden transitions, etc.)
- **Repository/**: repository interfaces (contracts, not Doctrine)
- **Service/**: domain services (pure business rules, orchestration-free)
- **Lore/**: POESI symbolic data and domain narrative structures

## What does NOT belong here
- Controllers / Requests / Responses
- Doctrine repositories or QueryBuilder usage
- Symfony-specific services, events, or security voters
- External I/O (mail, http clients, filesystem)

## Dependency rules
- Domain must not depend on Http or Infrastructure.
- Domain should remain framework-agnostic whenever possible.

## Notes
If a class needs Symfony/Doctrine to work, it probably does not belong here.
