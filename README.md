# Bookmark Application


## Application de marque-pages

L'application permet d'enregistrer des favoris avec des notes associées (ou des notes seules), avec la possibilité de les classer par page. Ces dernières sont découpées en listes.

- Page => une ou plusieurs listes => un ou plusieurs favoris

## Stack technique

### Pour la partie Back

* PHP 8.0
* Symfony 5
* ORM Doctrine
* Bundles : doctrine/doctrine-fixtures-bundle, knplabs/knp-menu-bundle, symfony/web-server-bundle, symfony/mailer

### Pour la partie Front

* Moteur de templates Twig
* JavaScript Vanilla
* Bootstrap 4
* Fontawesome

### Pour la BDD

* SQLite

## Procédure technique d'installation

## Prérequis

* PHP8.0 avec les principaux modules nécessaires à l'installation de Symfony 5.
* Installation des modules complémentaires php8.0-gd et php8.0-sqlite3.

## Installation

Une fois le serveur Apache/Nginx configuré pour le projet :
### Copier/coller le fichier .env en .env.local, puis :
* Ajouter la ligne `DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"` et vérifier que l'utilisateur du serveur (ex : www-data) est bien accès à la base en écriture. Commenter toutes autres lignes comportant la même clé `DATABASE_URL`.
* Modifier la ligne `APP_ENV=dev` en `APP_ENV=prod`.
* Ajouter la ligne `MAILER_DSN=smtp://user:password@ssl0.ovh.net:999` en modifiant l'identifiant, le mot de passe et le port avec votre configuration SMTP.
* Modifier au besoin les lignes présentes dans la section `Divers`.

### Effectuer la commande `php bin/console cache:clear`