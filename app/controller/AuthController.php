<?php

require_once 'app/model/UserManager.php';

class AuthController{

    public function authentication(){
        global $twig;
        unset($_SESSION['tmp']);
        echo $twig->render('authentication.twig');
    }

    public function userLogin(){
        global $router;
        $loginErrors = [];
        $loginSuccess = [];
        // CHECKING INPUTS
        if (!isset($_POST['username']) || empty ($_POST['username']))
        {
            $loginErrors[] = "Merci de renseigner votre nom d'utilisateur.";
        }
        if (!isset($_POST['password']) || empty ($_POST['password']))
        {
            $loginErrors[] = "Merci de renseigner votre mot de passe.";
        }
    
        //NO INPUT ERRORS -- CHECKING VALUES
        if (empty ($loginErrors)) {	
            $user = new UserManager();
            $username = $_POST['username'];
            $password = $_POST['password'];
            $passwordHash = sha1($password);
            $checkUser = $user->checkUser($username, $passwordHash);
    
            if (!$checkUser){
                $loginErrors[] = "Nom d'utilisateur et/ou mot de passe incorrect.";
            }
            else {			
                $userIsActive = $user->getUserIsActive($username);
    
                if($userIsActive == 0){
                    $loginErrors[] = "Votre compte est désactivé, merci de prendre contact avec un administrateur.";
                }
                else{
                    $userid = $user->getUserId($username);
                    $userIsAdmin = $user->getUserIsAdmin($username);
                    $_SESSION['username'] = $username;
                    $_SESSION['userIsAdmin'] = $userIsAdmin;
                    $_SESSION['userId'] = $userid;
                    $loginSuccess = "Vous êtes connecté. \nBon retour parmis nous " . $username." !";
                }
            }
    
        }
        $_SESSION['tmp'] = array_merge(['loginSuccess'=>$loginSuccess,'loginError'=>$loginErrors]);
        header('Location:'.$router->generate('authentication'));
    }

    public function userRegister(){
        global $router;
        $registerErrors = [];
        $registerSuccess = [];

        //CHECKING INPUTS
        if (empty ($_POST['usermail']) || empty ($_POST['username']) || empty ($_POST['password']) || empty ($_POST['passwordCheck'])){
            $registerErrors[] = "Merci de renseigner tous les champs.";
        }        

        //NO ERRORS -- CHECKING INPUTS VALUES
        if (empty($registerErrors)){
            
            $user = new UserManager();
            $usermail = $_POST['usermail'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $passwordCheck = $_POST['passwordCheck'];
        
            $validUsermail = preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $usermail);
            if (!$validUsermail){
                $registerErrors[] = "L'email n'est pas valide, merci de la vérifier.";
            }

            $checkUsermail = $user->checkUsermail($usermail);
            if ($checkUsermail){
                $registerErrors[] = "Cet adresse email est déjà utilisée.";
            }
           
            $passwordHash = sha1($password);
            $checkUsername = $user->checkUser($username, $passwordHash);
            if ($checkUsername){
                $registerErrors[] = "Ce nom d'utilisateur est déjà utilisé.";
            }
            
            $checkPasswords = $password === $passwordCheck;
            if (!$checkPasswords){
                $registerErrors[] = "Les mots de passes ne correspondent pas, vérifiez vos entrées.";
            }
            //ALL INPUTS ARE VALID -- INSERT INTO DB 
            if (!$checkUsername && !$checkUsermail && $checkPasswords && $validUsermail){
                $user->registerUser($usermail, $username, $passwordHash);
                $registerSuccess = "Votre compte a été créé avec succès.\n Bienvenue ". $username. "!";
            }
        }
        $_SESSION['tmp'] = array_merge(['registerSuccess'=>$registerSuccess,'registerError'=>$registerErrors]);
        header('Location:'.$router->generate('authentication'));
    }

    public function disconnect(){
        global $router;
        //UNSET SESSION INFOS
        unset($_SESSION['username'], $_SESSION['userIsAdmin'], $_SESSION['userId']);
        //REDIRECTING USER
        header('Location:'.$router->generate('home'));
    }
}