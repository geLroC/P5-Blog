# Projet 5 - Blog PHP

Projet numéro 5 du parcours Développeur d'applications PHP/Symfony d'OpenClassrooms.<br/>
Créer un blog **_from scratch_** en PHP orienté objet avec une architecture MVC.

<h2>Pré-requis</h2>

- Serveur Web (Apache, PHP, MySQL)
- Terminal
- Gestionnaire de dépendance `Composer`

<h2>Installation</h2>

- Clonez le repository sur votre machine,
- Ouvrez un terminal dans le dossier du projet précédemment cloné
- Éxécutez la commande `composer install`
- Créez une base de donnée
- Téléchargez et importez la base de donnée via le fichier `/database/p5blog.sql`
- Dans le dossier `/app/config/`
   - Ouvrez le fichier `dbconfig.php` afin de configurer les informations de connexion à votre base de données<br/>
   - Ouvrez le fichier `mailerconfig.php` afin de renseigner les informations de votre compte de messagerie<br/>
   - Ouvrez le fichier `routerconfig.php` afin de renseigner le chemin du dossier public

<h2>Administration</h2>
Des comptes administrateurs et utilisateurs sont déjà présents dans la base de donnée.

- Le compte administrateur est `Admin/tata`
- Le compte utilisateur est `User/tata`

Vous pouvez bien-sûr créer vos propres identifiants.

<h2>Dépendances utilisées</h2>


**Twig**

**Altorouter**

**PHPMailer**