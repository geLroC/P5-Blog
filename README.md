# P5-Blog
Projet numéro 5 du parcours Développeur d'applications PHP/Symfony d'OpenClassrooms.<br/>
Créer un blog **_from scratch_** en PHP orienté objet avec une architecture MVC.<br/><br/>

<h2>Installation</h2>

- Clonez le repository sur votre machine,

- Télchargez le fichier `/database/p5blog.sql` et importez la base de données sur votre serveur,

- Dans le fichier `/app/config/dbconfig.php` renseignez les informations nécessaires à la connexion à votre base de données<br/>
- Dans le fichier `/app/config/mailerconfig.php` renseignez les informations du compte de messagerie,<br/>
- Dans le fichier `/app/config/routerconfig.php` renseignez le chemin du dossier public<br/>

<h2>Administration</h2>
Des comptes administrateurs et utilisateurs sont déjà présents dans la base de donnée.

- Le compte administrateur est `Admin/tata`
- Le compte utilisateur est `User/tata`

Vous pouvez bien-sûr créer vos propres identifiants.

<h2>Dépendances utilisées</h2>


**Twig**&emsp;&emsp;&emsp;&emsp;`composer require twig/twig`

**Altorouter**&ensp;&emsp;`composer require altorouter/altorouter`

**PHPMailer**&ensp;&emsp;`composer require phpmailer/phpmailer`