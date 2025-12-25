# Lore Reference — Moods

Moods are a fixed reference catalog used by POESI to express a poem's atmosphere.
This table is synchronized from a versioned JSON file.

## Source of truth

`resources/lore/moods.initial.json`

## Database table

Table: `mood`

Columns:
- `id` (int, PK)
- `mood_key` (varchar(100), unique, NOT NULL)
- `label` (varchar(160), NOT NULL)
- `description` (text, NULL)
- `icon` (varchar(255), NULL)

## Sync command (fixtures)

### Dry-run:
```bash
php bin/console app:lore:sync-moods --dry-run
```

### Sync (upsert by key):
```bash
php bin/console app:lore:sync-moods
```

### Insert only:
```bash
php bin/console app:lore:sync-moods --insert-only
```

### Purge (remove rows not present in JSON):
```bash
php bin/console app:lore:sync-moods --purge
```

## Sync policy

- JSON is the source of truth.
- The command performs an upsert based on key.
- --purge is optional and should be used carefully.