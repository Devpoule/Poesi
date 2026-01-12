# Lore Reference — Totems

Totems are a fixed reference catalog used by POESI as the symbolic identity chosen by an author.
Each totem represents a writing posture, a tone, and a poetic inclination.

This table is synchronized from a versioned JSON file.

## Source of truth

`resources/lore/totems.initial.json`

## Database table

Table: `totem`

Columns:
- `id` (int, PK)
- `totem_key` (varchar(100), unique, NOT NULL)
- `name` (varchar(120), NOT NULL)
- `description` (text, NULL)
- `picture` (varchar(255), NULL)

## Sync command (fixtures)

### Dry-run:
```bash
php bin/console app:lore:sync-totems --dry-run
```

### Sync (upsert by key):
```bash
php bin/console app:lore:sync-totems
```

### Insert only:
```bash
php bin/console app:lore:sync-totems --insert-only
```

### Purge (remove rows not present in JSON):
```bash
php bin/console app:lore:sync-totems --purge
```

## Sync policy

- JSON is the source of truth.
- The command performs an upsert based on key.
- --purge is optional and should be used carefully.