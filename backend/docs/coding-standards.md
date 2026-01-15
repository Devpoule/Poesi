# POESI — Standards de code (backend PHP)

Objectif : garder un code homogène, auto-documenté, lisible par l’équipe et par les outils (IDE, static analysis).

## Base
- **PSR-12** pour le style global (indentation, imports, nombres de lignes, ordre des use).
- Typage strict partout : signatures, propriétés, retours, génériques PHPStan (si applicable).
- Noms parlants (PascalCase pour classes, camelCase pour méthodes/props, snake_case uniquement en SQL).

## PHPDoc : quand et comment
- **Classes** : brève description + `@extends` / `@implements` si génériques.
- **Méthodes** : documenter seulement si la signature ne suffit pas (règle métier, effets de bord, pré/post-conditions, exceptions levées).
- **Propriétés** : utile pour préciser les types génériques ou collections (`@var Collection<int, Poem>`).
- **Exceptions** : `@throws` dès qu’une exception est partie du contrat métier.
- **DTO / Request objects** : préciser le shape attendu (`@param array{title:string, mood?:string}` si non typé).
- Pas de PHPDoc redondant avec la signature (éviter “@param string $name” inutile).

## Conventions métier
- **Domain** : services stateless, exceptions dédiées (`App\Domain\Exception\*`), pas de dépendance à l’infrastructure.
- **Application/Http** : controllers fins, délèguent au domaine. Valider tôt (FormRequest/DTO) et mapper les erreurs domaine → HTTP.
- **Référentiels lore** : les JSON sous `backend/resources/lore/*.initial.json` sont canoniques et immuables ; nouvelle version = nouveau fichier.

## Erreurs & réponses API
- Format JSON unique (voir `backend/docs/api-errors.md`).
- Codes stables (`code`) pour le frontend, messages localisables.
- Mapping exceptions → réponses dans les listeners (pas d’echo/var_dump).

## Tests
- Préférer les tests de domaine (sans I/O) pour la logique.
- Pour HTTP : couvrir les happy/edge cases, vérifier la forme de réponse (status, code, message, data, errors).

## Commentaires
- 1–2 lignes maximum avant un bloc métier non trivial (raison d’une règle, contournement connu).
- Pas de commentaires de complaisance (“// set foo”).

## Migrations & seeds
- Migrations idempotentes, nommées par use-case.
- Seeds : utiliser les commandes fournies (`app:seed-all`), pas de seeds implicites dans les tests.

## Postman / Newman
- Collection et environnement : `backend/docs/postman/*`. Garder les chemins à jour si de nouveaux endpoints sont exposés.

## Checklist PR (backend)
- [ ] Typage strict, pas de `@return mixed` caché.
- [ ] PHPDoc uniquement si la signature ne suffit pas.
- [ ] Exceptions documentées et mappées en codes d’erreur API.
- [ ] Pas de logique métier dans les controllers.
- [ ] Ressources lore : pas de modification des `*.initial.json` (créer une variante si besoin).
