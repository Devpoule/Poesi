# POESI

POESI est une application où les textes ne sont pas simplement publiés : **ils prennent leur envol**.  
Le lore sert à donner une **cohérence symbolique** aux éléments visibles (totems, moods, symboles, plumes), sans surcharge narrative.

Ce document décrit le **canon initial**.  
Il accompagne les fichiers JSON d'archive.

---

## Principes

- **Sobriété et prestige**
- **Perception > jugement**
- **Pas de hiérarchie d'auteurs**
- **Canon versionné et immuable**

---

## Référentiels canoniques (JSON)

Les éléments de l'univers sont définis dans des fichiers JSON d'archive :

- `api/resources/lore/totems.initial.json`
- `api/resources/lore/moods.initial.json`
- `api/resources/lore/symbols.initial.json`
- `api/resources/lore/feathers.initial.json`
- `api/resources/lore/relics.initial.json`

Règles :
- `*.initial.json` = **immuable**
- Toute évolution = nouveau fichier (`*.v2.json`, `*.expanded.json`, etc.)
- Le runtime peut évoluer, le canon initial ne change pas

---

## Totems

Un **totem** représente **d'où parle un texte** : posture, énergie, angle d'expression.

Totem zéro : **l'Oeuf** (attente, potentiel).

Totems fondateurs :
- Moineau
- Chouette
- Faucon
- Corbeau
- Perroquet
- Cygne

Un auteur peut avoir un totem dominant, mais un texte peut en exprimer un autre.

---

## Moods

Un **mood** décrit la tonalité perçue d'un texte.  
Il ne décrit jamais l'auteur.

Un mood **Neutre** existe pour l'attente ou l'indétermination volontaire.

---

## Symbols

Les **symboles** sont des marqueurs visuels universels liés à l'envol.

Exemples canoniques :
- Ailes
- Plume
- Tourbillon
- Météore
- Horizon

Ils doivent rester sobres, immédiatement lisibles.

---

## Feathers

Les **plumes** reconnaissent une résonance, pas une qualité littéraire.

Canon initial :
- **Plume de Bronze** - premiers échos
- **Plume d'Argent** - circulation du texte
- **Plume d'Or** - impact durable

---

## Relics

Les **reliques** incarnent des moments symboliques, rares et non compétitifs.

Exemples canoniques :
- Plume de Phénix - renaissance
- Écaille de Dragon - endurance
- Ailes de Pégase - élévation
- Griffe de Lycan - laisser sa marque
- Corne de Licorne - magie, féerie

---

## Intention UX

POESI doit rester :
- intuitif
- lisible
- calme
- cohérent

Le lore sert l'expérience.  
Il ne la parasite jamais.
