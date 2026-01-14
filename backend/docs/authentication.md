# Authentication (JWT)

POESI uses JWT for API authentication (LexikJWTAuthenticationBundle).

## Setup

Generate JWT keys (once):

```
php bin/console lexik:jwt:generate-keypair
```

Ensure these env vars are set (`.env.local`):

```
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your-passphrase
```

## Register (public)

`POST /api/users`

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

Accepted alternatives:
- `passwordHash` instead of `password` (already hashed).
- `totemId` instead of `totemKey`.

## Login (public)

`POST /api/login_check`

```json
{
  "email": "user@example.com",
  "password": "your-password"
}
```

Response includes a JWT token:

```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
  "refreshToken": "base64url...",
  "refreshTokenExpiresAt": "2026-02-01T12:00:00+00:00"
}
```

## Refresh token (public)

`POST /api/token/refresh`

```json
{
  "refreshToken": "base64url..."
}
```

Response:

```json
{
  "status": true,
  "type": "success",
  "code": "SUCCESS",
  "message": "Token refreshed.",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
    "refreshToken": "base64url...",
    "refreshTokenExpiresAt": "2026-02-01T12:00:00+00:00"
  },
  "errors": null,
  "meta": null
}
```

## Authenticated requests

Add the token to the `Authorization` header:

```
Authorization: Bearer <token>
```

## Access control

- `POST /api/users`: public
- `POST /api/login_check`: public
- `POST /api/token/refresh`: public
- `/api/*`: requires `ROLE_USER`

See `backend/docs/auth-roles.md` for role-specific guidance.

## Rate limiting

- Login is throttled (5 attempts / minute).
- API requests are rate-limited (120 requests / minute).

## Account lockout

Accounts are locked after 5 failed login attempts.
Successful login resets the counter.

## Key rotation

Rotate JWT keys by generating a new keypair and updating env vars:

```
php bin/console lexik:jwt:generate-keypair
```

Deploy the new `private.pem` / `public.pem` and update `JWT_*` env vars.
