# POESI API — Error Handling Guide

This project uses a single, consistent JSON error format for all endpoints.

## 1. Response format

All API responses follow the same structure:

- `status` (bool): success or failure
- `type` (string): UI feedback type (success, error, validation_error, conflict, warning, etc.)
- `code` (string): stable technical code for frontend (mobile + web)
- `message` (string|null): human-readable message
- `data` (mixed|null): payload on success, optional on error
- `errors` (array|null): field errors for validation, or details for debugging (optional)
- `meta` (array|null): pagination or extra info

---

## 2. Where exceptions should live

### 2.1 Domain exceptions (`App\Domain\Exception`)

Domain exceptions describe business situations and must remain HTTP-agnostic.  
They are usually lightweight (sometimes empty) and only exist as types.

Examples:
- `NotFound\PoemNotFoundException`
- `CannotDelete\CannotDeletePoemWithVotesException`
- `NotFound\AuthorNotFoundException`

Domain exceptions are thrown by **Domain Services**.

They do not know:
- HTTP status codes
- JSON structure
- UI feedback types

---

### 2.2 API exceptions (`App\Http\Exception`)

API exceptions describe HTTP-facing problems.  
They implement `ApiExceptionInterface` and carry:

- error code
- HTTP status
- UI feedback type
- optional safe message
- optional error details

Examples:
- `ValidationException`
- `UnauthorizedApiException`
- `ForbiddenApiException`
- `RateLimitedApiException`

These are typically thrown by:
- Request validation layer
- Security / authentication layer
- API infrastructure

---

## 3. Single exit point: `ApiExceptionSubscriber`

`ApiExceptionSubscriber` converts exceptions to `JsonResponse`:

1) If the exception implements `ApiExceptionInterface`  
   → build JSON directly from it

2) If the exception is a known domain exception  
   → map it to an API error response

3) Otherwise  
   → return `INTERNAL_ERROR` (500) and log the exception

Controllers should **not** contain generic try/catch blocks.

---

## 4. How to add a new error

### Case A: New domain rule (business)

1) Create a domain exception in `App\Domain\Exception`
2) Throw it from a Domain Service
3) Map it in `ApiExceptionSubscriber` to:
   - HTTP status
   - stable error code
   - UI feedback type

### Case B: Request payload invalid

1) Validate request in a Request object (`CreateXRequest`)
2) Throw `ValidationException` with field => message errors
3) Subscriber returns:
   - HTTP 400
   - `type = validation_error`

---

## 5. Recommended HTTP statuses

- 400 BAD_REQUEST: payload malformed or invalid
- 401 UNAUTHORIZED: not authenticated
- 403 FORBIDDEN: authenticated but not allowed
- 404 NOT_FOUND: resource does not exist
- 409 CONFLICT: domain rule violation
- 422 UNPROCESSABLE_ENTITY (optional): advanced validation
- 429 TOO_MANY_REQUESTS: rate limiting
- 500 INTERNAL_SERVER_ERROR: unexpected error

---

## 6. Logging strategy

- Domain exceptions: `info` or `notice`
- Validation errors: no logging (or `debug`)
- Unexpected errors: `error` with exception class and message

Sensitive data must never appear in API responses.
