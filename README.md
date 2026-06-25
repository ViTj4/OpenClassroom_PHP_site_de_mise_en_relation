# OpenClassroom_PHP_site_de_mise_en_relation

## TomTroc

Le but de ce projet est de réaliser un site permettant la mise en contact de
lecteurs, afin qu'ils puissent partager et échanger leurs livres.
Ce projet est un MVP, c'est-à-dire "Minimal Viable Product", une première
version qui doit fonctionner mais sera rapidement améliorée à l'avenir.

## Styles

Les styles principaux sont organises en Sass dans `public/assets/scss`.
Le fichier servi par l'application est `public/assets/css/main.css`.

## Base de donnees

Le schema SQL se trouve dans `database/schema.sql`.
Il cree la base `tom_troc` si besoin et la table `users`.

Les variables de connexion sont lues depuis un fichier `.env` a la racine du
projet. Un exemple est fourni dans `.env.example`.

Avec XAMPP, il peut etre importe depuis phpMyAdmin ou en ligne de commande :

```bash
mysql -u root tom_troc < database/schema.sql
```
