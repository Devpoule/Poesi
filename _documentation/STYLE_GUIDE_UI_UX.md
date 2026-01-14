## Guide de style — Poesi (draft)

But : rafraîchir l'UI/UX en s'appuyant sur les visuels fournis dans `_documentation/visuels`.

Contrainte principale : garder l'esprit chaleureux et organique des visuels tout en modernisant les proportions, l'espacement et la typographie pour un rendu plus frais sur web/mobile.

1) Moodboard (résumé)
- Tonalité : chaud, papier / sable / brun clair
- Accents : tons cuivrés / terre cuite pour CTAs
- Neutres : beiges-crème et cartes blanches légèrement crémées

2) Palette (proposition)
- Background principal : #F6F1EA (crème clair)
- Surface (cards): #FFFFFF
- Surface muted: #F1E8DD
- Accent principal: #B07A47 (cuivre chaud)
- Accent strong: #8F5C2B
- Accent soft: #EAD7BF
- Border: #E6DDD2
- Text primary: #2B221B
- Text secondary: #5A4F46

3) Typographie
- Famille : serif principal pour titres (Baskerville / Georgia fallback) pour conserver caractère poétique
- Sans-serif pour UI (europa/Inter fallback) possible pour légibilité — à tester
- Échelles (px) : display 32, title 26, body 16, caption 13, small 12

4) Espacements / grille
- Tokens spacing : xs 6, sm 10, md 16, lg 24, xl 32, xxl 40
- Layout : laisser `sidePercent` de 20% sur web (gauche/droite), `contentWidth` 60%, `maxWidth` 1200px

5) Composants (principes)
- `Screen` : contenu centré, largeur basée sur tokens; atmosphère visuelle en arrière-plan (dégradés/halos) reste, mais réduire débordements sur petits écrans.
- `Card` : coins arrondis (16-24px), bord 1px léger, ombre subtile; padding `md`.
- `Button` primaire : fond `accent`, texte clair, border-radius pill (999), padding md/ sm.
- `TabBar` : sur web, la barre flottante respectera `sidePercent` pour rester centrée avec le contenu.

6) Accessibilité
- Contraste : vérifier contraste des textes et CTAs; s'assurer que `accent` se distingue du fond.
- Taille minimale des cibles : 40x40px recommandé.

7) Livrables de la première itération
- Fichier tokens mis à jour (`frontend/src/support/theme/tokens.ts`) — couleurs, layout, spacing, typographie.
- Mise à jour de `Screen` + `app/(tabs)/_layout.tsx` + `TabItem` + `Button` + style `Home` pour illustrer la direction.
- Captures d'écran web/mobile et checklist d'accessibilité.

Prochaine étape : j'implémente le style guide dans le code (tokens + composants clés) et j'applique la nouvelle UI à `Home`. Je committe et pousse les changements, puis je lance l'app web pour vérification. Voulez-vous que j'applique directement la famille de polices serif pour tous les titres, ou préférez-vous tester un mix serif(titles)+sans(body) ?
