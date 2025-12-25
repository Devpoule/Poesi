# Domain

## Purpose
This directory contains the **core business model** of the application.

## Why this layer exists
The Domain is the most stable part of the system.  
It describes **what the app is** and **which rules must always hold**,
regardless of delivery (HTTP) or persistence (DB).

This layer must remain:
- expressive
- predictable
- framework-agnostic

## What belongs here
- **Entity/**  
  Domain entities with business meaning (state + invariants)

- **Enum/**  
  Domain enums (moods, rarities, statuses, etc.)

- **Exception/**  
  Domain errors describing invalid states or forbidden transitions  
  (HTTP-agnostic, business-oriented)

- **Repository/**  
  Repository interfaces (contracts only, no Doctrine)

- **Service/**  
  Domain services (business rules, validation, consistency)

- **Lore/**  
  Canonical symbolic data and narrative structures (POESI lore)  
  Used as **source of truth** for referential data (moods, feathers, symbols, relics, totems)

## What does NOT belong here
- Controllers / Requests / Responses
- Doctrine repositories or QueryBuilder usage
- Symfony-specific services, events, or security voters
- External I/O (mail, HTTP clients, filesystem)

## Dependency rules
- Domain must **not** depend on Http or Infrastructure.
- Domain should remain framework-agnostic whenever possible.

## Notes
If a class needs Symfony or Doctrine to work,
it probably does **not** belong here.
