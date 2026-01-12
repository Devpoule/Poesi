# Auth & Roles

This document lists every API endpoint and the required role(s).

## Roles

- `ROLE_USER`: base role for any authenticated account.
- `ROLE_WRITER`: can create/update/publish poems.
- `ROLE_ADMIN`: admin-only operations (lore CRUD, users list, global votes).

## Authentication

- Login endpoint: `POST /api/login_check` (public).
- Refresh endpoint: `POST /api/token/refresh` (public).
- For protected routes: `Authorization: Bearer <token>`.

## Public endpoints (no token)

- `POST /api/login_check`
- `POST /api/token/refresh`
- `POST /api/users` (registration)

## Users

- `GET /api/users`: `ROLE_ADMIN`
- `GET /api/users/options`: `ROLE_ADMIN`
- `GET /api/users/public`: public
- `GET /api/users/{id}`: owner or `ROLE_ADMIN`
- `POST /api/users`: public
- `PUT /api/users/{id}`: owner or `ROLE_ADMIN`
- `DELETE /api/users/{id}`: owner or `ROLE_ADMIN`

## Poems

- `GET /api/poems`: `ROLE_USER`
- `GET /api/poems/full`: `ROLE_USER`
- `GET /api/poems/{id}`: `ROLE_USER`
- `POST /api/poems`: `ROLE_WRITER`
- `PUT /api/poems/{id}`: owner + `ROLE_WRITER`
- `POST /api/poems/{id}/publish`: owner + `ROLE_WRITER`
- `DELETE /api/poems/{id}`: owner + `ROLE_WRITER`

## Feather votes

- `GET /api/feather-votes`: `ROLE_ADMIN`
- `GET /api/feather-votes/{id}`: owner or `ROLE_ADMIN`
- `POST /api/feather-votes`: `ROLE_USER`
- `GET /api/feather-votes/poem/{poemId}`: `ROLE_USER`
- `GET /api/feather-votes/user/{userId}`: owner or `ROLE_ADMIN`
- `DELETE /api/feather-votes/{id}`: owner or `ROLE_ADMIN`

## User rewards

- `GET /api/users/{userId}/rewards`: owner or `ROLE_ADMIN`
- `POST /api/users/{userId}/rewards`: `ROLE_ADMIN`
- `DELETE /api/user-rewards/{id}`: owner or `ROLE_ADMIN`

## Lore catalog

- `GET /api/lore`: `ROLE_USER`

## Feathers

- `GET /api/feathers`: `ROLE_USER`
- `GET /api/feathers/{id}`: `ROLE_USER`
- `POST /api/feathers`: `ROLE_ADMIN`
- `PUT /api/feathers/{id}`: `ROLE_ADMIN`
- `DELETE /api/feathers/{id}`: `ROLE_ADMIN`

## Moods

- `GET /api/moods`: `ROLE_USER`
- `GET /api/moods/{id}`: `ROLE_USER`
- `POST /api/moods`: `ROLE_ADMIN`
- `PUT /api/moods/{id}`: `ROLE_ADMIN`
- `DELETE /api/moods/{id}`: `ROLE_ADMIN`

## Symbols

- `GET /api/symbols`: `ROLE_USER`
- `GET /api/symbols/{id}`: `ROLE_USER`
- `POST /api/symbols`: `ROLE_ADMIN`
- `PUT /api/symbols/{id}`: `ROLE_ADMIN`
- `DELETE /api/symbols/{id}`: `ROLE_ADMIN`

## Relics

- `GET /api/relics`: `ROLE_USER`
- `GET /api/relics/{id}`: `ROLE_USER`
- `POST /api/relics`: `ROLE_ADMIN`
- `PUT /api/relics/{id}`: `ROLE_ADMIN`
- `DELETE /api/relics/{id}`: `ROLE_ADMIN`

## Totems

- `GET /api/totems`: `ROLE_USER`
- `GET /api/totems/{id}`: `ROLE_USER`
- `POST /api/totems`: `ROLE_ADMIN`
- `PUT /api/totems/{id}`: `ROLE_ADMIN`
- `DELETE /api/totems/{id}`: `ROLE_ADMIN`

## Rewards

- `GET /api/rewards`: `ROLE_USER`
- `GET /api/rewards/{id}`: `ROLE_USER`
- `POST /api/rewards`: `ROLE_ADMIN`
- `PUT /api/rewards/{id}`: `ROLE_ADMIN`
- `DELETE /api/rewards/{id}`: `ROLE_ADMIN`

## Notes

- Ownership checks are enforced via voters on user/poem/vote/reward resources.
- For create poem/vote, the user is taken from the JWT; optional IDs are rejected if they don't match.
