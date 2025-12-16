# Exception Mapping Table

## A) Domain exceptions (App\Domain\Exception)

| Exception class                           | HTTP | code                 | type      | Notes |
|-------------------------------------------|------|----------------------|-----------|------|
| NotFound\AuthorNotFoundException                   | 404  | AUTHOR_NOT_FOUND     | error     | Requested author does not exist |
| NotFound\PoemNotFoundException                     | 404  | POEM_NOT_FOUND       | error     | Requested poem does not exist |
| NotFound\TotemNotFoundException                    | 404  | TOTEM_NOT_FOUND      | error     | Requested totem does not exist |
| NotFound\RewardNotFoundException                   | 404  | REWARD_NOT_FOUND     | error     | Requested reward does not exist |
| NotFound\FeatherVoteNotFoundException              | 404  | FEATHER_VOTE_NOT_FOUND | error   | Vote does not exist |
| NotFound\AuthorRewardNotFoundException             | 404  | AUTHOR_REWARD_NOT_FOUND | error  | Association does not exist |
| NotFound\UserNotFoundException                     | 404  | USER_NOT_FOUND       | error     | Requested user does not exist |
| CannotDelete\CannotDeletePoemWithVotesException        | 409  | POEM_HAS_VOTES       | conflict  | Prevent deletion while votes exist |
| DuplicateVoteException (optional future)  | 409  | DUPLICATE_VOTE       | conflict  | Prevent same user voting twice |

## B) API exceptions (App\Http\Exception)

| Exception class            | HTTP | code               | type             | Notes |
|---------------------------|------|--------------------|------------------|------|
| ValidationException       | 400  | VALIDATION_FAILED  | validation_error | Field-level errors in `errors` |
| UnauthorizedApiException  | 401  | UNAUTHORIZED       | error            | Missing/invalid token |
| ForbiddenApiException     | 403  | FORBIDDEN          | error            | Not allowed |
| RateLimitedApiException   | 429  | RATE_LIMITED       | warning          | Too many requests |
