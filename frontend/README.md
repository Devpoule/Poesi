# Frontend

Cette base front utilise **Expo** (React Native) avec **RN Web** pour cibler le navigateur, et **Mercure** pour le temps réel.

## Structure

- `app/` : routes Expo Router (écrans + navigation)
- `App.tsx` : conservé pour compatibilité, mais l'entrée est `expo-router/entry`
- `src/domain/` : langage métier (types, règles, lore)
- `src/http/` : points d'entrée UI (screens, navigation)
- `src/infrastructure/` : détails techniques (API, storage, temps réel)
- `src/support/` : utilitaires transverses (formatage, guards, erreurs)
- `src/bootstrap/` : initialisation et wiring

## Commandes

```bash
cd frontend
npm install
npm run dev
npm run web
npm run ios
npm run android
```

## Routage

Les routes Expo Router se trouvent dans `app/` et importent les `Screen` de `src/http/screen/`.

## Temps réel (Mercure)

- Web : `EventSource` natif.
- Mobile : ajouter un polyfill EventSource (ex. `react-native-sse`) puis brancher `connectMercure`.

## Environnement

Dans `src/support/config/env.ts` :
- `baseUrl` : URL de l'API Symfony
- `mercureHubUrl` : URL du hub Mercure
