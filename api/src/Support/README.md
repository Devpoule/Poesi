# Support

## Purpose
This directory contains cross-cutting helpers and utilities shared by multiple layers.

## Why this layer exists
Some code is not business logic (Domain) and not delivery (Http) and not technical integration (Infrastructure),
but is still reusable and needed to keep other layers clean.

## What belongs here
- **Util/**: generic helpers (string normalization, formatting, small pure functions)
- Small shared primitives that do not belong to any domain concept

## What does NOT belong here
- Business rules disguised as helpers
- Infrastructure-specific utilities (Doctrine helpers, Symfony container access, etc.)
- Large "God helpers" that become a dumping ground

## Design rules
- Keep utilities small and explicit.
- Prefer pure functions/stateless helpers.
- If it grows into a feature: promote it into Domain or Infrastructure.
