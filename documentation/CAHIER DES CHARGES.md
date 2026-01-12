# POESI - Cahier des Charges

## 1. Présentation générale

POESI est une application poétique **sobre, symbolique et non compétitive**, dédiée à l'écriture et à la perception sensible des textes.

Elle ne vise ni la performance, ni la mise en concurrence des auteurs.  
Elle privilégie :
- la **résonance** plutôt que l'évaluation,
- la **perception** plutôt que le jugement,
- la **lenteur** plutôt que la viralité.

L'expérience repose sur :
- un **éditeur poétique immersif**,
- des **référentiels symboliques canoniques** (totems, moods, plumes, symboles, reliques),
- une **révélation progressive** de la perception collective (selon les cas),
- une **esthétique de l'envol**, élégante et silencieuse.

---

## 2. Principes fondateurs

- Pas de score.
- Pas de classement.
- Pas de "meilleur auteur".
- Pas de gamification agressive.

À la place :
- des **symboles**,
- des **indices sensibles**,
- une **lecture active et respectueuse**.

Le texte n'est jamais noté.  
Il est **perçu**.

---

## 3. Référentiels canoniques (Lore)

Tous les éléments symboliques reposent sur un **canon versionné**, stocké dans des fichiers JSON immuables.

### 3.1 Référentiels existants

- **Totems** - posture d'écriture
- **Moods** - tonalité choisie ou perçue
- **Feathers (Plumes)** - marqueurs de résonance
- **Symbols** - figures de l'envol
- **Relics** - marqueurs rares et non compétitifs

Chaque référentiel :
- possède sa **table dédiée**
- est synchronisé via une **commande CLI**
- peut évoluer via de nouveaux fichiers JSON (fixtures++)

---

## 4. Identité de l'auteur

Dans l'application, un "auteur" correspond à un **utilisateur** avec le rôle `ROLE_WRITER`.

### 4.1 Totem

- Chaque auteur choisit un **totem principal**.
- Le totem représente une **posture d'écriture**, pas une identité figée.
- Le totem est visible mais **non hiérarchique**.

### 4.2 État initial

- Totem par défaut : **l'Oeuf**
- Il symbolise l'attente, la potentialité, l'entrée dans l'écriture.

Aucune animation obligatoire à ce stade :  
l'UX reste **légère et respectueuse**.

---

## 5. Système des Moods (ambiguïté volontaire / perception)

Un texte possède un **mood** (tonalité).

### 5.1 Règle principale

Le mood peut être :

1) **Défini par l'auteur** (choix explicite)  
ou  
2) **Déduit par les lecteurs** si l'auteur laisse le mood à **Neutre**  
(option par défaut)

### 5.2 Déduction par les lecteurs (si mood = Neutre)

- Le texte commence avec un mood **non révélé / indéterminé**
- Les lecteurs perçoivent le texte via une **interaction UX indirecte**  
  (questions, ressentis, gestes simples)
- Les interactions contribuent à révéler progressivement un **mood dominant**

### 5.3 Moods canoniques

Rouge, Orange, Jaune, Vert, Bleu, Indigo, Violet, Blanc, Noir, Gris, **Neutre**.

---

## 6. Plumes (Feathers)

Les plumes ne sont pas des récompenses quantitatives.  
Elles signalent une **résonance**, pas une qualité.

### 6.1 Canon initial

- **Bronze** - premiers échos
- **Argent** - circulation du texte
- **Or** - impact durable

### 6.2 Règle de révélation

- Les plumes d'un texte peuvent être **cachées** tant que le lecteur n'a pas interagi
- Une fois l'interaction faite, la plume (ou le niveau) peut être révélée

*(La mécanique exacte de calcul ou d'attribution peut évoluer, mais l'intention reste : perception plutôt que compétition.)*

---

## 7. Symboles

Les symboles représentent des **formes universelles de l'envol** :

- Ailes
- Tourbillon
- Météore
- Horizon
- Halo

Ils peuvent apparaître :
- lors de la lecture,
- lors de la révélation d'un mood,
- lors de la révélation d'une plume,
- lors d'événements rares.

Ils sont **visuels**, jamais explicatifs.

---

## 8. Reliques

Les reliques sont :
- rares,
- non compétitives,
- attribuées à des **moments symboliques**.

Exemples :
- renaissance après silence,
- constance sur la durée,
- élévation exceptionnelle.

Une relique **ne se chasse pas**.  
Elle survient.

---

## 9. Éditeur poétique

### 9.1 Fonctionnalités

- Écriture plein écran
- Interface silencieuse
- Sauvegarde automatique
- Aucun compteur intrusif

### 9.2 Gestion du mood dans l'éditeur

- Par défaut, le mood est **Neutre**
- L'auteur peut :
  - garder Neutre (mood déduit ensuite par les lecteurs)
  - sélectionner un mood explicitement (mood fixé par l'auteur)

### 9.3 Philosophie

- Pas d'analyse en temps réel intrusive.
- Pas de "score stylistique".
- L'écriture reste **libre**, sans feedback normatif.

Les mécanismes avancés (rythme, répétition, musicalité) pourront être explorés plus tard, de façon non prescriptive.

---

## 10. Lecture & galerie

- Galerie personnelle des textes
- Lecture épurée
- Révélation progressive (mood / plumes) selon les cas
- Export texte / image
- Partage optionnel, jamais obligatoire

---

## 11. Architecture technique

### 11.1 Backend

- Symfony
- Architecture hexagonale légère
- API-first (JSON only)

### 11.2 Couches

- **Domain** - règles, lore, invariants
- **Http** - controllers, requests, responses
- **Infrastructure** - Doctrine, DB, listeners
- **Command** - sync lore, maintenance
- **Support** - utilitaires génériques (quand nécessaire)

### 11.3 Référentiels

Chaque référentiel possède :
- CRUD API (admin / interne)
- Commande `app:lore:sync-*`
- Table dédiée
- Documentation lore associée

---

## 12. Base de données

Tables principales (entities) :
- `user`
- `poem`
- `totem`
- `mood`
- `feather`
- `symbol`
- `relic`
- `reward`
- `refresh_token`

Tables relationnelles :
- `feather_vote`
- `user_reward`
- `user_relic`

---

## 13. Feuille de route

### Phase 1 - Fondations
- Lore canonique
- Référentiels DB
- Sync commands
- Architecture API

### Phase 2 - Lecture & perception
- Lecture silencieuse
- Gestes de perception
- Révélation mood / plume quand applicable

### Phase 3 - Écriture
- Éditeur minimal
- Sauvegarde
- Galerie personnelle

### Phase 4 - Symbolique avancée
- Symboles d'envol
- Reliques
- Événements rares

### Phase 5 - Raffinement
- UX
- lenteur
- cohérence esthétique

---
