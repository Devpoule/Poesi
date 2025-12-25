# Lore Reference — Feathers

Feathers are a fixed reference catalog used by POESI to describe a poem’s recognition level.
This table is synchronized from a versioned JSON file.

## Source of truth

`resources/lore/feathers.initial.json`

## Database table

Table: `feather`

Columns:
- `id` (int, PK)
- `feather_key` (varchar(100), unique, NOT NULL)
- `label` (varchar(160), NOT NULL)
- `description` (text, NULL)
- `icon` (varchar(255), NULL)

## Sync command (fixtures)

### Dry-run:
```bash
php bin/console app:lore:sync-feathers --dry-run
```

### Sync (upsert by key):
```bash
php bin/console app:lore:sync-feathers
```

### Insert only:
```bash
php bin/console app:lore:sync-feathers --insert-only
```

### Purge (remove rows not present in JSON):
```bash
php bin/console app:lore:sync-feathers --purge
```

## Sync policy

- JSON is the source of truth.
- The command performs an upsert based on key.
- --purge is optional and should be used carefully.
