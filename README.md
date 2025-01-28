
# FMR

https://fmr.pydonze.fr/

![Statut](https://img.shields.io/badge/statut-en%20cours-yellow)

## Description

FMR est une application web développée en PHP avec le framework Symfony. Ce projet est actuellement en cours de développement et vise à fournir une solution clé en main pour acheter/vendre des produits entre particuliers.

## Fonctionnalités implémentées

- [x] Authentification des utilisateurs
- [x] Gestion des produits
- [x] Filtrage et recherche des produits
- [x] Mise en ligne d'articles (CRUD)
- [ ] Système de paiement
- [ ] Espace personnel (gestion des données personnelles)
- [ ] Intégration avec des services tiers

## Installation

Pour installer et exécuter ce projet en local :

1. Clonez le dépôt :

  ```bash
   git clone https://github.com/pierreyvesdonze/fmr.git
  ```

2. Installez les dépendances :

  ```bash
   composer install
  ```

3. Configurez les variables d'environnement en dupliquant le fichier `.env` :

  ```bash
   cp .env .env.local
  ```

4. Exécutez les migrations de la base de données :

  ```bash
   php bin/console doctrine:migrations:migrate
  ```

5. Lancer le serveur Symfony

  ```bash
   php -S localhost:8000 -t public OU symfony server:start
  ```
