# Postman & Newman

This project ships a ready-to-import Postman collection and environment file.

Files:
- `api/docs/postman/poesi.postman_collection.json`
- `api/docs/postman/poesi.environment.json`

## Quick start (no JavaScript)
1) Import the collection and the environment in Postman.
   - File -> Import -> select `api/docs/postman/poesi.postman_collection.json`
   - File -> Import -> select `api/docs/postman/poesi.environment.json`
2) Select the "POESI Local" environment.
3) Run login, then manually copy the token into:
   - `admin_token` for admin requests
   - `user_token` for user requests
4) For any request that needs an ID (user, poem, reward, etc.), copy the ID
   from the response into the environment variables.
5) You can also edit the login credentials in the environment:
   - `admin_email`, `admin_password`
   - `user_email`, `user_password`

## Pagination & sorting
List endpoints now accept:
- `page` (default varies per endpoint)
- `limit` (default varies; max 200)
- `sort` (whitelisted per endpoint)
- `direction` (`ASC` or `DESC`)

Paginated responses include `meta.pagination`.

## Reset data
Use the seed command to get a consistent local dataset:

```
symfony console app:seed-all
```

Options:
- `--dry-run`
- `--insert-only`
- `--purge` (for lore)
- `--no-migrate`

## Newman (CLI)
If you want to run the collection from the command line:

```
npm install -g newman
newman run api/docs/postman/poesi.postman_collection.json \
  -e api/docs/postman/poesi.environment.json
```

You can keep the environment variables empty and fill them by hand,
or export a populated environment from Postman before running Newman.
