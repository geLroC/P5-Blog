<?php 
if (!isset($_SESSION['username']))  {
    session_start();
}

//autoload
require 'vendor/autoload.php';
require_once 'app/routes/Route.php';
require_once 'app/controller/FrontController.php';
require_once 'app/controller/BackController.php';

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

//TWIG SETUP
$loader = new \Twig\Loader\FilesystemLoader('app/views/templates');
$twig = new \Twig\Environment($loader,[
'cache' => false, //__DIR__.'/tmp'
'debug' => true, //{{ dump ($var) }}
]);
$twig->addGlobal('session', $_SESSION);
$twig->addExtension(new \Twig\Extension\DebugExtension());



//ALTOROUTER
$router = new AltoRouter();
$data = require 'app/config/routerconfig.php';

$router->setBasePath($data['basepath']);

foreach(Route::getRoutes() as $route){
    $router->map(...$route);
}

$match = $router->match();

define('BASE_URL', dirname($_SERVER['SCRIPT_NAME']));

//ADDING ROUTES IN A SESSION VAR
$_SESSION['routes'] = array_merge(
    ['home'=>$router->generate('home')],
    ['homepage'=>$router->generate('homepage')],
    ['authentication'=>$router->generate('authentication')],
    ['login'=>$router->generate('login')],
    ['register'=>$router->generate('register')],
    ['disconnect'=>$router->generate('disconnect')],
    ['userlist'=>$router->generate('userlist')],
    ['deleteUser'=>$router->generate('deleteUser')],
    ['setUserAdmin'=>$router->generate('setUserAdmin')],
    ['unsetUserAdmin'=>$router->generate('unsetUserAdmin')],
    ['activateUser'=>$router->generate('activateUser')],
    ['deactivateUser'=>$router->generate('deactivateUser')],
    ['account'=>$router->generate('account')],
    ['passedit'=>$router->generate('passedit')],
    ['postlist'=>$router->generate('postlist')],
    ['pagenumber'=>$router->generate('pagenumber')],
    ['post'=>$router->generate('post')],
    ['newpost'=>$router->generate('newpost')],
    ['addpost'=>$router->generate('addpost')],
    ['deletepost'=>$router->generate('deletepost')],
    ['editpost'=>$router->generate('editpost')],
    ['postEdition'=>$router->generate('postEdition')],
    ['commentlist'=>$router->generate('commentlist')],
    ['newcomment'=>$router->generate('newcomment')],
    ['deletecomment'=>$router->generate('deletecomment')],
    ['validcomment'=>$router->generate('validcomment')],
    ['commentnumber'=>$router->generate('commentnumber')],
    ['mail'=>$router->generate('mail')]
    );

if (!$match){
    header('HTTP/1.0 404 NOT FOUND');
}
else{
    list($controller, $action) = explode('#', $match['target']);

    $controller = new $controller();
    
    if (is_callable(array($controller, $action))){
        call_user_func_array(array($controller, $action), array($match['params']));

    }
    else{
        echo 'Erreur 500 - La requête n\'a pas pu être traitée.';
    }

}