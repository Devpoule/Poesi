# Lore Reference — Relics

Relics are a fixed reference catalog used by POESI as symbolic rewards.
This table is synchronized from a versioned JSON file.

## Source of truth

`resources/lore/relics.initial.json`

## Database table

Table: `relic`

Columns:
- `id` (int, PK)
- `relic_key` (varchar(140), unique, NOT NULL)
- `label` (varchar(180), NOT NULL)
- `description` (text, NULL)
- `picture` (varchar(255), NULL)
- `rarity` (varchar(60), NOT NULL)

## Sync command (fixtures)

### Dry-run:
```bash
php bin/console app:lore:sync-relics --dry-run
```

### Sync (upsert by key):
```bash
php bin/console app:lore:sync-relics
```

### Insert only:
```bash
php bin/console app:lore:sync-relics --insert-only
```

### Purge (remove rows not present in JSON):
```bash
php bin/console app:lore:sync-relics --purge
```

## Sync policy

- JSON is the source of truth.
- The command performs an upsert based on key.
- --purge is optional and should be used carefully.
