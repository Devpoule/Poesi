# Frontend Conventions (React Native / Expo)

## Nommage
- Composants React: PascalCase (`PageLayout`, `CardGrid`, `SidePanel`).
- Hooks: `use` + CamelCase (`useHomeRevealAnimation`).
- Fichiers: alignés sur le composant/hook principal (`CardGrid.tsx`, `useHomeRevealAnimation.ts`).
- Props/variables: camelCase; constantes de thèmes ou mappings: SCREAMING_SNAKE pour valeurs fixes si partagé globalement.

## Structure des pages
- Utiliser `PageLayout` pour l’ossature: SafeArea + scroll + `PageHeader`.
- Envelopper les blocs logiques dans `Section` (cartes neutres).
- Grilles/listes: privilégier `CardGrid` pour les items (2–4 colonnes responsive) plutôt que des grilles maison.
- Panneaux latéraux: `SidePanel` (Mood, Totem, etc.) pour harmoniser ombre/rayons.

## Styles et réutilisation
- Toujours passer par `support/theme/tokens` pour couleurs/typo/spacing.
- Pas de styles en dur pour ombres/bordures/rayon: réutiliser `Section`, `SidePanel`, `CardPortrait`, `CardGrid`.
- Grilles: ne pas recréer de `flexWrap` ad hoc; utiliser `CardGrid` ou extraire un composant dédié.
- Boutons/CTA: aligner sur les composants existants (`Button` si présent) ou définir des variantes (primary/secondary/ghost) centralisées.

## Commentaires & JSDoc
- Ajouter des commentaires concis uniquement là où le comportement n’est pas évident.
- Les composants partagés exposés doivent avoir un JSDoc succinct (intention, props clés).

## Nettoyage / DRY
- Éviter les doublons de sections “header + body”: factoriser via `PageLayout` + `Section`.
- Supprimer les imports inutilisés et espacer le code selon les tokens (pas de padding “magiques”).
- Mutualiser les mappings d’items (lore, discover, filtres) dans des constantes dédiées.

## À faire en continu
- Lors de l’ajout d’une page: partir de `PageLayout`, ajouter des `Section`, puis des grilles/lists via composants partagés.
- Lors d’un refacto: déplacer le style commun dans un composant ou un style partagé avant de dupliquer.
