# Http (API-First)

## Purpose
This directory contains the **API delivery layer**:
controllers, input validation, and output formatting.

## API-First scope
This project is **API-first**:
- No Twig rendering
- No server-side HTML pages
- Controllers produce JSON responses only
- Frontend is external (SPA, mobile app, etc.)

## Why this layer exists
HTTP is a delivery mechanism.

This layer translates:
- incoming HTTP requests -> application/domain intent
- domain results -> consistent JSON responses

## What belongs here
- **Controller/**  
  Route handlers (thin, orchestration only)

- **Request/**  
  Request DTOs, validation models, input normalization

- **Response/**  
  Response DTOs / presenters / serializers

- **Exception/**  
  HTTP-facing exceptions (bad request, unauthorized, forbidden, etc.)

- **Security/**  
  HTTP/security integration (access rules, voters, authenticators)

## What does NOT belong here
- Business rules (Domain)
- Doctrine queries or persistence logic (Infrastructure)
- Heavy transformations reused elsewhere  
  (prefer Domain services or dedicated mappers)

## Controller design rules
Controllers must remain thin:

1) Validate input  
2) Call a domain service  
3) Return a JSON response

- Avoid putting business decisions in controllers
- Never return raw Entities directly
