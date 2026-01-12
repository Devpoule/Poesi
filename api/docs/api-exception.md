# Exception Mapping Reference

This document summarizes how exceptions map to HTTP responses in the current codebase.

## A) Domain exceptions (`App\Domain\Exception`)

### Mapping by family

| Family | HTTP | type | Notes |
|--------|------|------|-------|
| NotFoundException | 404 | error | Resource not found |
| ConflictException | 409 | warning | Business conflict |
| CannotDeleteException | 409 | warning | Deletion blocked by business rules |
| CannotPublishException | 409 | warning | Publication blocked by business rules |
| Other DomainException | 400 | error | Default mapping |

### Current domain exceptions

NotFound:
- `NotFound\UserNotFoundException`
- `NotFound\PoemNotFoundException`
- `NotFound\TotemNotFoundException`
- `NotFound\FeatherNotFoundException`
- `NotFound\MoodNotFoundException`
- `NotFound\SymbolNotFoundException`
- `NotFound\RelicNotFoundException`
- `NotFound\RewardNotFoundException`
- `NotFound\FeatherVoteNotFoundException`
- `NotFound\UserRewardNotFoundException`

Conflict:
- `Conflict\EmailAlreadyUsedException`
- `Conflict\FeatherKeyAlreadyExistsException`
- `Conflict\MoodKeyAlreadyExistsException`
- `Conflict\RelicKeyAlreadyExistsException`
- `Conflict\SymbolKeyAlreadyExistsException`

CannotDelete:
- `CannotDelete\CannotDeleteUserException`
- `CannotDelete\CannotDeleteSymbolInUseException`
- `CannotDelete\CannotDeleteRelicInUseException`
- `CannotDelete\CannotDeleteMoodInUseException`
- `CannotDelete\CannotDeleteFeatherInUseException`
- `CannotDelete\CannotDeletePoemWithVotesException`

CannotPublish:
- `CannotPublish\CannotPublishPoemException`
- `CannotPublish\CannotPublishWithoutTotemException`

Other (default 400):
- `CannotUpdate\CannotUpdatePoemException`
- `CannotVote\CannotVoteOwnPoemException`

---

## B) API exceptions (`App\Http\Exception`)

| Exception class | HTTP | code | type | Notes |
|----------------|------|------|------|------|
| ValidationException | 422 | INVALID_PAYLOAD | validation_error | Field-level errors in `errors` |

Custom HTTP exceptions should extend `AbstractApiException` and implement `ApiExceptionInterface`.

---

## C) DB exceptions

| Exception class | HTTP | code | type | Notes |
|----------------|------|------|------|------|
| ForeignKeyConstraintViolationException | 409 | DELETE_CONFLICT | warning | Deletion blocked by FK constraints |

---

## D) HttpExceptionInterface

| HTTP | code | type | Notes |
|------|------|------|------|
| 404 | RESOURCE_NOT_FOUND | error | Route not found |
| 405 | METHOD_NOT_ALLOWED | error | Method not allowed |
| other | HTTP_ERROR | error | Generic HTTP error |
