# Frontend POESI - Guide rapide

Ce document explique l'architecture frontend, les principales zones, et le role de chaque partie.

## 1) Structure generale

Le frontend est un projet Expo + React Native + Expo Router.
La structure principale se trouve dans `frontend/`.

- `app/`: routing Expo Router (pages et layouts).
- `src/`: code applicatif (UI, hooks, theme, domain, infra).
- `src/support/theme/`: theme global (dark/light + mood).
- `src/http/screen/`: ecrans et composants de presentation.
- `src/bootstrap/`: providers globaux (auth, theme).
- `src/infrastructure/`: acces API, storage, serialisation.
- `api/resources/lore/`: donnees lore (moods, totems, etc.) utilisees dans l'UI.

## 2) Navigation (Expo Router)

`frontend/app/_layout.tsx` est le layout racine.
- `ThemeProvider` et `AuthProvider` y encapsulent l'app.
- `AuthGate` gere les redirections login / tabs.

`frontend/app/(tabs)/_layout.tsx` definit les onglets principaux:
- home, poems, write, guide, profile.

`frontend/app/(tabs)/guide/*` contient les pages du guide lore.
`frontend/app/(auth)/*` contient login/inscription.

## 3) Theme (dark/light + mood global)

Le theme est defini dans `frontend/src/support/theme/tokens.ts`.
- `ThemeProvider` expose `mode` (dark/light) et `accentKey` (mood global).
- Les couleurs sont derivees dynamiquement avec un recoloring doux.
- L'accent est stocke via `MoodStorage` (`frontend/src/infrastructure/storage/MoodStorage.ts`).

La palette mood globale est affichee par le layout tabs:
`frontend/app/(tabs)/_layout.tsx` (panneau en marge a droite).

## 4) Ecrans principaux

### Accueil
`frontend/src/http/screen/Home/HomeScreen.tsx`
Contient les sections:
- hero (entree principale)
- highlights / parcours
- panel moods
- principes (lore)
- rituel

### Feed / Poems
`frontend/src/http/screen/Feed/FeedScreen.tsx`
Liste de poemes + filtres de mood.

### Ecrire
`frontend/src/http/screen/Write/WriteScreen.tsx`
`WriteEditor` gere l'editor, sauvegarde auto, mood pour l'oeuvre.
Le mood actif du site est utilise pour la teinte de l'interface.

### Guide / Glossaire
`frontend/src/http/screen/Lore/*`
Chaque page lit les JSON dans `api/resources/lore/`.

### Profil
`frontend/src/http/screen/Profile/ProfileScreen.tsx`
Affiche la session, totem, stats et le toggle dark/light.

## 5) Providers et etats globaux

### Auth
`frontend/src/bootstrap/AuthProvider.tsx`
- charge le token en storage
- expose login/logout

### Theme
`frontend/src/support/theme/tokens.ts`
- mode (light/dark)
- accent global (mood)
- palette calculee a chaque changement

## 6) Composants reutilisables

- `Screen`: wrapper commun avec SafeArea + atmosphere.
- `Button`: boutons theme-aware.
- `TabItem`: icones des onglets.
- `HomeMoodSection`: panneau mood (utilise globalement).

## 7) Donnees lore

`api/resources/lore/*`
- `moods.initial.json`, `totems.initial.json`, `feathers.initial.json`, etc.
Utilises par:
- `frontend/src/http/screen/Lore/loreData.ts`
- `frontend/src/http/screen/Write/utils/moodLore.ts`

## 8) Cycle de rendu d'un ecran

1. L'ecran est monte via `app/*`.
2. `Screen` applique le theme + atmos.
3. L'ecran compose ses sections avec styles.
4. Les hooks (view models) appellent les usecases ou API.

## 9) Adaptation responsive

Plusieurs composants utilisent `useWindowDimensions` pour adapter:
- padding lateraux (`Screen`)
- largeur des cartes (`Home` / `Lore`)
- onglets (`TabItem`)

## 10) Points d'entree importants

- `frontend/app/_layout.tsx`: racine, providers, AuthGate.
- `frontend/app/(tabs)/_layout.tsx`: onglets + panneau mood global.
- `frontend/src/support/theme/tokens.ts`: theme global + mood.

## 11) Comment ajouter un ecran

1. Creer `frontend/app/(tabs)/nouvel-ecran.tsx`
2. Creer son composant dans `src/http/screen/...`
3. Ajouter l'onglet si besoin dans `(tabs)/_layout.tsx`

## 12) Tests / verifications

Le projet n'a pas de tests automatiques.
Verification manuelle:
- `npm --prefix frontend run dev`
- tester navigation, mood, dark/light.
