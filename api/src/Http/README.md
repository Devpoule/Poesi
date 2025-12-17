# Http (API-First)

## Purpose
This directory contains the API delivery layer: controllers, input validation and output formatting.

## API-First scope
This project is **API-first**:
- No Twig rendering
- No server-side HTML pages
- Controllers produce JSON responses only
- Frontend is external (SPA, mobile app, etc.)

## Why this layer exists
HTTP is a delivery mechanism. This layer translates:
- incoming HTTP requests -> application/domain intent
- domain results -> consistent JSON responses

## What belongs here
- **Controller/**: route handlers (thin, orchestration only)
- **Request/**: request DTOs / validation models / input normalization
- **Response/**: response DTOs / presenters / serializers
- **Exception/**: HTTP-facing exceptions (bad request, not found, conflict, etc.)
- **Security/**: HTTP/security integration (access rules, voters, authenticators)

## What does NOT belong here
- Business rules (Domain)
- Doctrine queries or persistence logic (Infrastructure)
- Heavy transformations reused elsewhere (prefer dedicated mappers or Support)

## Controller design rules
- Controllers must be thin:
  1) Validate input
  2) Call an application/domain service
  3) Return a JSON response
- Avoid putting business decisions in controllers.
- Avoid returning raw Entities directly.

---

# JSON Conventions

## Response envelope
All successful responses must return JSON using a predictable envelope:

- `data`: the actual payload
- `meta`: optional metadata (pagination, counts, timings, etc.)

Example:
```json
{
  "data": {
    "id": 123,
    "pseudo": "Tito",
    "moodColor": "indigo"
  },
  "meta": {
    "requestId": "..."
  }
}
