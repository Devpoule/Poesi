# UI Components (frontend/src/http/components)

This folder hosts small, reusable fragments to keep pages SOLID/DRY:

- `PageHeader`: Title + optional subtitle/action area. Keeps typography/colors consistent.
- `PageLayout`: Wrapper around `Screen` + `PageHeader` to standardize page skeletons (scroll, header, content).
- `Section`: Neutral card container with consistent radius/border/padding. Use it to wrap logical blocks.
- `SidePanel`: Elevated panel sized for side content (e.g., Mood palette, totem card), with shadow/border.
- `CardPortrait`: Portrait frame (trading-card ratio) for all visuals (totems, relics, feathers, symbols).
- `CardGrid`: Responsive grid (2–4 cols) for lore items (image/accent/tag), supports selection/onPress.

Guidelines:
- Prefer composing pages with `PageHeader` + multiple `Section` blocks.
- For side content (left/right), wrap in `SidePanel` to keep spacing/shadows uniform.
- Avoid custom shadows/radii on pages; rely on these components for consistency.
