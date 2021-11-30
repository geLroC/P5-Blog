<?php

require_once 'app/model/UserManager.php';

class AuthController{

    public function authentication(){
        global $twig;
        echo $twig->render('authentication.twig');
        unset($_SESSION['tmp']);
    }

    public function userLogin(){
        $loginErrors = [];
        $loginSuccess = [];
        // Checking inputs
        if (!isset($_POST['username']) || empty ($_POST['username']))
        {
            $loginErrors[] = "Merci de renseigner votre nom d'utilisateur.";
        }
        if (!isset($_POST['password']) || empty ($_POST['password']))
        {
            $loginErrors[] = "Merci de renseigner votre mot de passe.";
        }
    
        //no input errors, checking values
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
                $userid = $user->getUserId($username);
                $userIsAdmin = $user->getUserIsAdmin();
                $userIsActive = $user->getUserIsActive();
    
                if($userIsActive == 0){
                    $loginErrors[] = "Votre compte est désactivé, merci de prendre contact avec un administrateur.";
                }
                else{
                    $_SESSION['username'] = $username;
                    $_SESSION['userIsAdmin'] = $userIsAdmin;
                    $_SESSION['userId'] = $userid;
                    $loginSuccess = "Vous êtes connecté. \nBon retour parmis nous " . $_POST['username']." !";
                }
            }
    
        }
        $_SESSION['tmp'] = array_merge(['loginSuccess'=>$loginSuccess,'loginError'=>$loginErrors]);
        header('Location:'.$_SESSION['routes']['authentication']);
    }

    public function userRegister(){
        $registerErrors = [];
        $registerSuccess = [];
        //Checking inputs
        if (!isset($_POST['usermail']) || empty ($_POST['usermail'])){
            $registerErrors[] = "Merci de renseigner votre email.";
        }
        if (!isset($_POST['username']) || empty ($_POST['username'])){
            $registerErrors[] = "Merci de renseigner votre nom d'utilisateur.";
        }
        if (!isset($_POST['password']) || empty ($_POST['password'])){
            $registerErrors[] = "Merci de renseigner un mot de passe.";
        }
        if (!isset($_POST['passwordCheck']) || empty ($_POST['passwordCheck'])){
            $registerErrors[] = "Merci de renseigner la validation de mot de passe.";
        }
        

        //No errors, checking inputs values
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
            $checkUsername = $user->checkUsername($username);
            if ($checkUsername){
                $registerErrors[] = "Ce nom d'utilisateur est déjà utilisé.";
            }
            $checkPasswords = $password === $passwordCheck;
            if (!$checkPasswords){
                $registerErrors[] = "Les mots de passes ne correspondent pas, vérifiez vos entrées.";
            }
            //All inputs are valid, insertion into DB
            if (!$checkUsername && !$checkUsermail && $checkPasswords && $validUsermail){
                $passwordHash = sha1($password);
                $user->registerUser($usermail, $username, $passwordHash);
                $registerSuccess = "Votre compte a été créé avec succès.\n Bienvenue ". $_POST['username'] . "!";
            }
        }
        $_SESSION['tmp'] = array_merge(['registerSuccess'=>$registerSuccess,'registerError'=>$registerErrors]);
        header('Location:'.$_SESSION['routes']['authentication']);
    }

    public function disconnect(){
        //Unset session infos
        unset($_SESSION['username'], $_SESSION['userIsAdmin'], $_SESSION['userId']);
        //Redirecting user
        header('Location:'.$_SESSION['routes']['home']);
    }
}