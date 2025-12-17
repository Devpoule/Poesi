# POESI — Lore (Canon fondateur)

POESI est une application où les textes ne sont pas simplement publiés : **ils prennent leur envol**.  
Le lore de POESI sert un objectif simple : donner une **cohérence symbolique** aux éléments visibles de l’expérience (totems, moods, symboles, plumes), sans transformer l’app en encyclopédie.

Ce document décrit le **canon initial** (v1). Il accompagne des fichiers JSON immuables.

---

## Principes

- **Sobriété et prestige** : peu d’éléments, mais chacun est signifiant.
- **Pas de hiérarchie d’auteurs** : aucun totem, mood ou plume ne “classe” les personnes.
- **Perception > jugement** : POESI décrit des ressentis et des postures, pas une valeur littéraire.
- **Canon versionné** : les référentiels initiaux sont archivés et ne doivent pas être modifiés.

---

## Référentiels canoniques (JSON)

Les données “univers” sont définies dans des fichiers JSON d’archive :

- `totems.initial.json`
- `moods.initial.json`
- `symbols.initial.json`
- `feathers.initial.json`

Règle :  
- `*.initial.json` = **immuable** (archive).  
- Toute évolution = nouveau fichier (`*.v2.json`, `*.expanded.json`, etc.).  
- Le runtime (DB, API, UI) peut évoluer, mais **le canon initial reste intact**.

---

## Totems

Un **totem** est un compagnon symbolique : il représente **d’où parle** un texte (posture, énergie, manière d’entrer en résonance).

### Totem zéro : l’Œuf
L’Œuf incarne l’attente, le potentiel, l’avant-forme.  
Il sert d’état neutre : **en attendant d’éclore**.

### Totems fondateurs
- **Moineau** — sincérité nue, justesse, voix basse.
- **Chouette** — lucidité intérieure, analyse, précision calme.
- **Faucon** — tension maîtrisée, frappe nette, concision volontaire.
- **Corbeau** — profondeur assumée, gravité digne, traversée de l’ombre.
- **Perroquet** — vibration expressive, musicalité, répétitions assumées.
- **Cygne** — grâce maîtrisée, harmonie, fluidité formelle.

> Un auteur peut avoir un totem dominant, mais un texte peut naturellement “parler” depuis un autre totem.

---

## Moods

Un **mood** exprime la tonalité perçue d’un texte : **comment il est ressenti**, pas ce que l’auteur “est”.

### Mood neutre
Le mood **Neutre** existe pour l’attente : le texte n’a pas encore trouvé sa tonalité (ou elle est volontairement laissée ouverte).

### Moods initiaux
Le canon initial propose des moods sobres, immédiatement compréhensibles, utilisables en UI et en narration.

---

## Symbols

Les **symboles** sont des marqueurs visuels universels, liés à l’idée d’**envol**.  
Ils enrichissent l’expérience (écrans, animations, “états de grâce”) sans introduire de folklore inutile.

Exemples de symboles canoniques :  
**Ailes**, **Plume**, **Tourbillon**, **Météore**, **Horizon**.

---

## Feathers

Les **plumes** ne notent pas la qualité littéraire.  
Elles reconnaissent un **impact**, une **résonance**, un mouvement.

Canon initial :
- **Plume de Bronze** — premiers lecteurs, premiers échos.
- **Plume d’Argent** — le texte circule, résonne.
- **Plume d’Or** — le texte marque durablement.

> La rareté doit venir de la sobriété : pas de surenchère “platine/diamant”.

---

## Intention UX

POESI doit rester :
- **intuitif** (compréhensible sans manuel),
- **raffiné** (symbolique cohérente),
- **calme** (pas de gamification bruyante),
- **lisible** (référentiels nets, peu d’exceptions).

Le lore est un cadre : il sert l’expérience, il ne la parasite pas.

---
