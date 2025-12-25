# Exception Mapping Table

## A) Domain exceptions (`App\Domain\Exception`)

| Exception class                                   | HTTP | code                      | type     | Notes |
|--------------------------------------------------|------|---------------------------|----------|------|
| NotFound\AuthorNotFoundException                 | 404  | AUTHOR_NOT_FOUND          | error    | Requested author does not exist |
| NotFound\UserNotFoundException                   | 404  | USER_NOT_FOUND            | error    | Requested user does not exist |
| NotFound\PoemNotFoundException                   | 404  | POEM_NOT_FOUND            | error    | Requested poem does not exist |
| NotFound\TotemNotFoundException                  | 404  | TOTEM_NOT_FOUND           | error    | Requested totem does not exist |
| NotFound\FeatherNotFoundException                | 404  | FEATHER_NOT_FOUND         | error    | Requested feather does not exist |
| NotFound\MoodNotFoundException                   | 404  | MOOD_NOT_FOUND            | error    | Requested mood does not exist |
| NotFound\SymbolNotFoundException                 | 404  | SYMBOL_NOT_FOUND          | error    | Requested symbol does not exist |
| NotFound\RelicNotFoundException                  | 404  | RELIC_NOT_FOUND           | error    | Requested relic does not exist |
| NotFound\RewardNotFoundException                 | 404  | REWARD_NOT_FOUND          | error    | Requested reward does not exist |
| NotFound\FeatherVoteNotFoundException            | 404  | FEATHER_VOTE_NOT_FOUND    | error    | Vote does not exist |
| NotFound\AuthorRewardNotFoundException           | 404  | AUTHOR_REWARD_NOT_FOUND   | error    | Association does not exist |
| CannotDelete\CannotDeletePoemWithVotesException  | 409  | POEM_HAS_VOTES            | warning  | Prevent deletion while votes exist |
| CannotVote\CannotVoteTwiceException (optional)   | 409  | DUPLICATE_VOTE            | warning  | Prevent same user voting twice |
| Canon\ImmutableCanonException                    | 409  | CANON_IMMUTABLE           | warning  | Attempt to modify initial canon |

---

## B) API exceptions (`App\Http\Exception`)

| Exception class            | HTTP | code               | type             | Notes |
|---------------------------|------|--------------------|------------------|------|
| ValidationException       | 422  | INVALID_PAYLOAD     | validation_error | Field-level errors in `errors` |
| BadRequestApiException    | 400  | BAD_REQUEST         | error            | Malformed request |
| UnauthorizedApiException  | 401  | UNAUTHORIZED        | error            | Missing or invalid token |
| ForbiddenApiException     | 403  | FORBIDDEN           | error            | Not allowed |
| RateLimitedApiException   | 429  | RATE_LIMITED        | warning          | Too many requests |

---

## C) DB exceptions

| Exception class                                 | HTTP | code            | type     | Notes |
|------------------------------------------------|------|-----------------|----------|------|
| ForeignKeyConstraintViolationException         | 409  | DELETE_CONFLICT | warning  | Deletion blocked by FK constraints |

---

## D) Fallback

| Case | HTTP | code | type | Notes |
|------|------|------|------|------|
| Unknown exception | 500 | INTERNAL_ERROR | error | Logged server-side |
