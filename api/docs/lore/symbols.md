# Lore Reference — Symbols

Symbols are a fixed reference catalog used by POESI to represent a poem’s elevation archetype.
This table is synchronized from a versioned JSON file.

## Source of truth

`resources/lore/symbols.initial.json`

## Database table

Table: `symbol`

Columns:
- `id` (int, PK)
- `symbol_key` (varchar(120), unique, NOT NULL)
- `label` (varchar(160), NOT NULL)
- `description` (text, NULL)
- `picture` (varchar(255), NULL)

## Sync command (fixtures)

### Dry-run:
```bash
php bin/console app:lore:sync-symbols --dry-run
```

### Sync (upsert by key):
```bash
php bin/console app:lore:sync-symbols
```

### Insert only:
```bash
php bin/console app:lore:sync-symbols --insert-only
```

### Purge (remove rows not present in JSON):
```bash
php bin/console app:lore:sync-symbols --purge
```

## Sync policy

- JSON is the source of truth.
- The command performs an upsert based on key.
- --purge is optional and should be used carefully.
