# POESI API — Error Handling Guide

This project uses a single, consistent JSON error format for all endpoints.

## 1. Response format

All API responses follow the same structure:

- `status` (bool): success or failure
- `type` (string): UI feedback type (success, error, validation_error, conflict, etc.)
- `code` (string): stable technical code for frontend (mobile + web)
- `message` (string|null): human-readable message
- `data` (mixed|null): payload on success, optional on error
- `errors` (array|null): field errors for validation, or details for debugging (optional)
- `meta` (array|null): pagination or extra info

## 2. Where exceptions should live

### 2.1 Domain exceptions (App\Domain\Exception)
Domain exceptions describe business situations and must remain HTTP-agnostic.
They are usually lightweight (sometimes empty) and only exist as types.

Examples:
- PoemNotFoundException
- CannotDeletePoemWithVotesException
- AuthorNotFoundException

Domain exceptions are thrown by Domain Services.

### 2.2 API exceptions (App\Http\Exception)
API exceptions describe HTTP-facing problems.
They implement ApiExceptionInterface and carry:
- error code
- HTTP status
- UI feedback type

Examples:
- ValidationException
- UnauthorizedApiException
- ForbiddenApiException

These are typically thrown by Request validation or Security layer.

## 3. Single exit point: ApiExceptionSubscriber

ApiExceptionSubscriber converts exceptions to JsonResponse:
1) If exception implements ApiExceptionInterface -> build JSON from it
2) If exception is a known domain exception -> map it to an API error response
3) Otherwise -> return INTERNAL_ERROR 500 and log the exception

Controllers should NOT repeat try/catch for standard cases.

## 4. How to add a new error

### Case A: New domain rule (business)
1) Create a domain exception in App\Domain\Exception
2) Throw it from a Domain Service method
3) Map it in ApiExceptionSubscriber to:
   - HTTP status
   - stable error code
   - UI type

### Case B: Request payload invalid
1) Validate request in a Request object (CreateXRequest)
2) Throw ValidationException with field => message errors
3) Subscriber will return 400 with type = validation_error

## 5. Recommended HTTP statuses

- 400 BAD_REQUEST: payload malformed, wrong types, missing required fields
- 401 UNAUTHORIZED: not authenticated
- 403 FORBIDDEN: authenticated but not allowed
- 404 NOT_FOUND: resource does not exist
- 409 CONFLICT: resource exists but cannot transition (e.g. delete poem with votes)
- 422 UNPROCESSABLE_ENTITY (optional): advanced validation
- 500 INTERNAL_SERVER_ERROR: unexpected error

## 6. Logging strategy

- Domain exceptions: usually "notice" or "info" depending on frequency
- Validation errors: usually no logging (or debug) to avoid noise
- Unexpected errors: "error" with exception class + message (+ stack trace if needed)
