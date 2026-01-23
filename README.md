# POESI

POESI is a minimalist poetic platform dedicated to short, sensitive, and embodied writing.  
It favors emotional resonance, attentive reading, and symbolic interaction over metrics,
performance, or social comparison.

POESI is neither a social network nor a publishing contest.  
It is a space for deposit, echo, and revelation.

---

## Purpose

POESI allows authors to write short poetic texts and share them in a calm,
intentional environment.  
Texts are not ranked, scored, or promoted. They are read, felt, and gradually
revealed through interaction.

The platform encourages:
- slow reading,
- intimate resonance,
- symbolic expression rather than explicit judgment.

---

## Core Concepts

### Author
- Chooses a **bird totem** as a lasting poetic signature.
- Writes short or fragmented texts.
- Keeps texts as **drafts** as long as they are not forgotten.
- Publishes when the text feels ready - or necessary.
- May suggest a Mood, or let readers reveal it.
- Can receive symbolic poetic tributes during special events.

---

### Totem
- A symbolic bird chosen by the Author.
- Acts as a visual and poetic identity marker.
- Persistent across the Author's profile and texts.
- Not a badge or reward, but a silent presence.

---

### Text
- Written by an Author.
- Can be draft, private, or public.
- Read by Readers.
- Can receive **Feathers**.
- Is imbued with a **Mood**, explicit or revealed.
- Evolves symbolically through interactions.

Each text is treated as a standalone micro-work: sober, readable, and elegant.

---

### Mood
- Represents the emotional atmosphere of a Text.
- Can be defined by the Author **or** revealed collectively by Readers.
- Is not known in advance by the Reader.
- Reveals itself **after interaction**, through a sensitive reading path.
- Evolves according to:
  - the number of Feathers received,
  - their category (Bronze, Silver, Gold),
  - the intrinsic nature of the Mood itself.

A Mood is never a fixed label, but a living interpretation.

---

### Feather
The Feather is the only form of appreciation.

- Replaces likes, scores, and popularity metrics.
- Is **anonymous**.
- Becomes visible only after interaction.
- Exists in three levels:
  - **Bronze**: sincere resonance.
  - **Silver**: strong emotional impact.
  - **Gold**: deep and lasting resonance.

Rare Feathers or symbolic tributes may appear during specific poetic events.

---

### Symbol
- A poetic item associated with a Mood.
- Refines and deepens the emotional reading.
- Adds an extra interpretative layer.
- Enhances meaning without explaining it.

---

## Philosophy

- No competition.
- No visible social metrics.
- No pressure to produce or to please.

POESI favors:
- slowness,
- attention,
- the beauty of writing and reading as gestures.

---

## In Short

POESI is a platform:
- to write quietly,
- to read attentively,
- to feel without imposing,
- to share without exposure.

A poetic, evolving, and restrained application -  
where echo matters more than volume.

---

# Developer Guide

This section is intended for developers working on the POESI codebase.
It documents how to install, run, and understand the technical structure
of the project.

---

## Tech Stack

- PHP 8.2+
- Symfony 6.x
- Doctrine ORM
- JSON API (API-first approach)
- PHPUnit (tests)

---

## Quickstart

### Requirements
- PHP 8.2 or higher
- Composer
- Symfony CLI (recommended)
- A database (MySQL or PostgreSQL)

### Installation

```bash
git clone https://github.com/your-organization/poesi.git
cd poesi
cd backend

composer install
cp .env .env.local
# configure DATABASE_URL

symfony serve

```

### Authentication (JWT)

POESI uses JWT for API authentication.

- Register: `POST /api/users`
- Login: `POST /api/login_check`
  - Auth header: `Authorization: Bearer <token>`

Example login payload:

```json
{
  "email": "user@example.com",
  "password": "your-password"
}
```

Example register payload:

```json
{
  "email": "user@example.com",
  "password": "your-password",
  "roles": ["ROLE_USER", "ROLE_WRITER"],
  "pseudo": "Writer",
  "moodColor": "blue",
  "totemKey": "egg"
}
```

See `backend/docs/authentication.md` for details.
See `backend/docs/auth-roles.md` for role-specific guidance.

### Local seeding & Postman testing

For a consistent local dataset:

```bash
symfony console app:seed-all
```

Postman collection and environment files are available in:
- `backend/docs/postman/poesi.postman_collection.json`
- `backend/docs/postman/poesi.environment.json`

See `backend/docs/postman.md` for step-by-step usage.

### Architecture Overview

POESI follows a clean, domain-oriented, API-first architecture.

#### Domain/
Pure business logic: entities, value objects, domain exceptions.
This layer is framework-agnostic.

#### Http/
Controllers, Request DTOs, Response mappers, API entry points.

#### Infrastructure/
Persistence (Doctrine), external services, framework implementations.

#### Command/
CLI commands for sync and maintenance.

#### Support/
Shared utilities and cross-cutting helpers.

Controllers remain thin.
Business rules never depend on the framework.
