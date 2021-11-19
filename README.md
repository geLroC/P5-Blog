# P5-Blog
Projet numéro 5 du parcours Développeur d'applications PHP/Symfony d'OpenClassrooms.<br/>
Créer un bloc **_from scratch_** en PHP orienté objet avec une architecture MVC.<br/><br/>

<h2>Installation</h2>

- Copiez le repository sur votre machine,

- Importez la base de données p5blog.sql sur votre serveur,

- Dans le fichier `/app/config/dbconfig.php` renseignez les informations nécessaires à la connexion à votre base de données<br/>
- Dans le fichier `/app/config/mailerconfig.php` renseignez les informations du compte de messagerie,<br/>
- Dans le fichier `/app/config/routerconfig.php` renseignez le chemin du dossier public<br/>

<h2>Administration</h2>
Des comptes administrateurs et utilisateurs sont déjà présents dans la base de donnée.

- Le compte administrateur est `Admin/tata`
- Le compte utilisateur est `pootest/tata`

Vous pouvez bien-sûr créer vos propres identifiants.

<h2>Dépendances utilisées</h2>


**Twig**  `composer require twig/twig`

**Altorouter**  `composer require altorouter/altorouter`

**PHPMailer**  `composer require phpmailer/phpmailer`