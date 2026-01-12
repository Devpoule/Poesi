# POESI API - Error Handling Guide

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

Domain exceptions are thrown by **Domain Services**.

They do not know:
- HTTP status codes
- JSON structure
- UI feedback types

Typical families:
- NotFound\*NotFoundException
- Conflict\*Exception
- CannotDelete\*Exception
- CannotPublish\*Exception
- Other DomainException types (e.g., CannotVote, CannotUpdate)

---

### 2.2 API exceptions (`App\Http\Exception`)

API exceptions describe HTTP-facing problems.  
They implement `ApiExceptionInterface` and carry:
- error code
- HTTP status
- UI feedback type
- optional safe message
- optional error details

At the moment, `ValidationException` is the primary API exception.
Custom HTTP exceptions should extend `AbstractApiException`.

---

## 3. Single exit point: `ApiExceptionSubscriber`

`ApiExceptionSubscriber` converts exceptions to a JsonResponse:

### Priority order

#### 1. ValidationException
- returns `validation_error` (HTTP 422)

#### 2. ApiExceptionInterface
- returns the exception-defined httpStatus, code, type

#### 3. DomainException
Mapped by family:
- NotFoundException -> 404
- ConflictException -> 409
- CannotDeleteException -> 409
- CannotPublish* -> 409
- default -> 400

Important: HTTP 409 responses use `type = warning`.

#### 4. ForeignKeyConstraintViolationException
- generic 409 `DELETE_CONFLICT` (type warning)

#### 5. HttpExceptionInterface
- 404 -> `RESOURCE_NOT_FOUND`
- 405 -> `METHOD_NOT_ALLOWED`
- other -> `HTTP_ERROR`

#### 6. Fallback
- 500 `INTERNAL_ERROR`

Controllers should not contain generic try/catch blocks.

---

## 4. How to add a new error

### Case A: New domain rule (business)

1) Create a domain exception in `App\Domain\Exception`
2) Throw it from a Domain Service
3) Ensure it provides a stable error code (`getErrorCode()`), or map it in the subscriber if needed
4) Subscriber returns:
   - HTTP status
   - stable error code
   - UI feedback type

### Case B: Request payload invalid

1) Validate request in a Request object (`CreateXRequest`)
2) Throw `ValidationException` with field => message errors
3) Subscriber returns:
   - HTTP 422
   - `type = validation_error`
   - `errors` contains field-level messages

---

## 5. Recommended HTTP statuses

- 400 BAD_REQUEST: payload malformed or invalid
- 401 UNAUTHORIZED: not authenticated
- 403 FORBIDDEN: authenticated but not allowed
- 404 NOT_FOUND: resource does not exist
- 409 CONFLICT: domain rule violation
- 422 UNPROCESSABLE_ENTITY: validation error
- 429 TOO_MANY_REQUESTS: rate limiting
- 500 INTERNAL_SERVER_ERROR: unexpected error

---

## 6. Logging strategy

Logging is centralized in ApiExceptionSubscriber:
- Validation exceptions: `warning` with `validation_errors`
- Domain exceptions: `info` with `domain_code`
- API exceptions: `warning` with `api_code` + `http_status`
- Unexpected errors: `error` with stack trace

Sensitive data must never appear in API responses.
