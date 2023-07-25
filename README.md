# Projet Symfony - Gestion d'utilisateurs et import de données depuis un fichier CSV

Ce projet Symfony a été développé dans le cadre de ma formation de concepteur développeur d'applications à l'école ENI. 

  ### Objectifs du projet : 
Plateforme web pour les stagiaires et anciens de l'ENI pour organiser des sorties.

  ### Problème à résoudre : 
Absence d'un canal de communication pour proposer ou consulter les sorties,
Difficulté à gérer les invitations en fonction de la situation géographique ou des intérêts des stagiaires.

## Les fonctionnalités que j'ai développées

1. Gestion des utilisateurs : Mise en place d'un système complet de gestion des utilisateurs en utilisant le composant Security de Symfony. Les utilisateurs peuvent s'inscrire, se connecter et récupérer leur mot de passe en cas d'oubli.

2. Routes et contrôleurs : Création des routes et des contrôleurs pour les différentes fonctionnalités de l'application, assurant une navigation fluide et une expérience utilisateur agréable.

3. Optimisation des requêtes SQL : Amélioration des performances de l'application en optimisant les requêtes SQL pour une récupération rapide des données depuis la base de données.

4. Import de données depuis un fichier CSV : Mise en place d'un formulaire permettant aux utilisateurs de charger un fichier CSV contenant des données à importer dans l'application. Traitement du fichier CSV avec le composant CSV de Symfony pour intégrer les données dans la base de données.

## Prérequis

- PHP 7.4 ou version ultérieure
- Composer
- MySQL

## Installation

1. Cloner ce dépôt GitHub sur votre machine locale.
2. Exécuter `composer install` pour installer les dépendances du projet.
3. Configurer la base de données en modifiant le fichier `.env` avec les informations de votre serveur MySQL.
4. Exécuter `php bin/console doctrine:database:create` pour créer la base de données.
5. Exécuter `php bin/console doctrine:migrations:migrate` pour effectuer les migrations.
6. Exécuter `php bin/console server:run` pour démarrer le serveur de développement.

## Contribuer

Les contributions à ce projet sont les bienvenues. Si vous souhaitez ajouter de nouvelles fonctionnalités, corriger des bugs ou améliorer la documentation, n'hésitez pas à créer une pull request.

## Auteurs

Dimitri F. et 2 autres collaborateurs



