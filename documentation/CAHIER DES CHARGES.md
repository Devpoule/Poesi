
---

# **📘 Application Poétique — Cahier des Charges Complet**

## 1. **Présentation Générale**

L’application poétique est un espace d’écriture immersive et prestigieuse, accessible à tous âges mais conçue avec une direction artistique raffinée.  
Le cœur du concept repose sur :

- un **éditeur poétique chromatique**,
    
- la **symbolique des oiseaux** comme totems personnels,
    
- des **animations de l’envol** lors des moments d’inspiration intense (états de grâce),
    
- une **expérience émotionnelle minimaliste et élégante**, sans imagerie enfantine.
    

L’objectif : **offrir un environnement inspirant où la création poétique devient un voyage émotionnel et visuel.**

---

## 2. **Modules Fonctionnels**

### 2.1. **Identité de l’Auteur**

- Choix d’un oiseau totem (liste restreinte et prestigieuse).
    
- Animation d’**éclosion de l’œuf** (naissance symbolique).
    
- Animation de **frappe de la pièce** comme sceau identitaire (sans personnage).
    
- Création du profil minimal : pseudo, oiseau, sceau automatique.
    

**Objectifs UX :** émerveillement, initiation, simplicité.

---

### 2.2. **Système Chromatique des Émotions**

Chaque couleur représente un état émotionnel et influence :

- l’ambiance visuelle de l’éditeur,
    
- les micro-animations,
    
- la tonalité perçue du texte.
    

Nuancier : Rouge, Orange, Jaune, Vert, Bleu, Indigo, Violet, Blanc, Noir, Gris.

---

### 2.3. **Éditeur Poétique**

Fonction centralisatrice :

- Écriture en plein écran.
    
- Sélecteur d’émotions (couleurs).
    
- Mini-analyse stylistique (détection de rimes, musicalité, répétitions).
    
- Déclenchement d’un **état de grâce** → animations d’envol (ailes, cyclone, météore, fusée) + apparition subtile de l’oiseau totem.
    
- Sauvegarde automatique.
    

**Exigence :** une interface épurée, élégante, jamais intrusive.

---

### 2.4. **Galerie et Lecture**

- Liste des poèmes sauvegardés.
    
- Visualisation avec animations douces.
    
- Export image / texte.
    
- Partage optionnel (réseaux sociaux ou export local).
    

---

### 2.5. **Onboarding**

Étapes :

1. Choix d’un oiseau (sans surcharger).
    
2. Animation d’éclosion → symbole d’éveil créatif.
    
3. Animation de la pièce frappée → création du sceau.
    
4. Arrivée dans l’éditeur.
    

Ton visuel : **prestige, silence, lumière, émergence.**

---

## 3. **Conception UX/UI**

### 3.1. Moodboard

- Éléments : lumière diffuse, lignes fines, symboles d’envol, palettes douces.
    
- Références : calligraphie moderne, design d’application de méditation premium.
    

### 3.2. Palette Chromatique

Pour chaque couleur → définir :

- teinte dominante
    
- variante claire
    
- micro-anim
    
- lien émotionnel
    

### 3.3. Composants UI

- Zone de texte immersive
    
- Toolbar minimaliste
    
- Indicateurs d’état de grâce
    
- Carte de poème (galerie)
    

---

## 4. **Architecture Technique**

### 4.1. Technologies

À choisir selon ambition :

- Web App PWA (React/Vue + animations Lottie/WebGL)
    
- Mobile natif ultérieurement
    

### 4.2. Modules backend

- Profil utilisateur
    
- Stockage des poèmes
    
- Analyse stylistique simple
    
- Historique des émotions
    

### 4.3. Base de données

Tables :

- `user` (pseudo, oiseau, sceau)
    
- `poem` (contenu, couleur dominante, date)
    
- `stats` (optionnel)
    

---

## 5. **Direction Artistique**

### 5.1. Oiseaux (totems)

Chaque oiseau doit être :

- identifiable par sa silhouette,
    
- stylisé avec élégance,
    
- associé à une qualité poétique.
    

Exemples :

- Aigrette — pureté
    
- Hirondelle — élan
    
- Faucon — précision
    
- Colombe — paix intérieure
    

### 5.2. Animations d’Éclat

- Éclosion : fissure lumineuse puis surgissement doux.
    
- Frappe de pièce : flash, impact, apparition du motif.
    
- État de grâce : ailes / cyclone / météore / fusée (choix selon oiseau).
    

---

## 6. **État de Grâce**

Déclenché si :

- répétitions harmoniques
    
- rimes récurrentes
    
- rythme équilibré
    
- cohérence chromatique
    

Effets :

- Montée de lumière
    
- Apparition du symbole d’envol
    
- L’oiseau totem se dessine brièvement
    
- Sceau qui pulse légèrement
    

---

## 7. **Feuille de Route (8 Sprints)**

_(résumé pour Obsidian)_

### **Sprint 1 – Fondations**

Oiseaux, couleurs, onboarding, moodboard, stack.

### **Sprint 2 – UX/UI**

Maquettes, storyboards, sceau.

### **Sprint 3 – Identité Auteur**

Éclosion, sceau, profil.

### **Sprint 4 – Éditeur Poétique**

Écriture, couleurs, analyses simples.

### **Sprint 5 – États de Grâce**

Symboles, logique d’activation.

### **Sprint 6 – Galerie**

Listes, export.

### **Sprint 7 – Artistique**

Textes narratifs, ajustements.

### **Sprint 8 – Tests & Optimisation**

Finalisation, Beta.