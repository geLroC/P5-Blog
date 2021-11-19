<?php

require_once './app/model/UserManager.php';
require_once './app/class/User.php';

class UserController{

    function userList(){
        global $twig;
        $userList = new UserManager();
        $list = $userList->getUserList();
        echo $twig->render('userlist.twig', ['userlist'=>$list]);
    }
    function userIsAdmin(){
        getUserIsAdmin();
    }
    function userIsActive(){
        getUserIsActive();
    }
    function setUserAdmin($userId, $userIsAdmin){
        global $twig;
        die(var_dump($userId, $userIsAdmin));
        $user = new UserManager();
        $user->setAdmin($userId, $userIsAdmin);
        echo $twig->render('userlist.twig', ['userlist'=>$list]);
    }
    function setUserActive($userId, $userIsActive){
        global $twig;
        $user = new UserManager();
        $user->setActive($userId, $userIsActive);
        echo $twig->render('userlist.twig', ['userlist'=>$list]);
    }
    function deleteUser($userId){
        $user = new UserManager();
        $user->userDelete($userId);
        $link = $router->generate('userlist');
        header('Location:'.$link);
    }
    function myAccount(){
        global $twig;
        unset($_SESSION['tmp']);
        $user = new UserManager();
        $currentUser = $user->getUserInfos($_SESSION['userId']);
        echo $twig->render('myaccount.twig', ['currentUser'=>$currentUser]);
    }
    function editPassword($userId){
        global $twig;
        unset($_SESSION['tmp']);
        $user = new UserManager();
        $userId = implode('',$userId);
        $currentUser = $user->getUserInfos($userId);
        $editPasswordErrors = [];
        $editPasswordSuccess = [];
        $password = $_POST['password'];
        $passwordCheck = $_POST['passwordCheck'];
        $userInfos = $user->getUserInfos($userId);
        $passInDb = $userInfos['userPassword'];
        $checkPasswords = $password === $passwordCheck;
        $passwordHash = sha1($password);
    
        //Checking inputs
        if (!isset($password) || empty ($password)){
            $editPasswordErrors[] = "Merci de renseigner un nouveau mot de passe";
        }
        if (!isset($password) || empty ($password)){
            $editPasswordErrors[] = "Merci de renseigner la validation du mot de passe.";
        }
        if (!empty($password) && !$checkPasswords){
            $editPasswordErrors[] = "Les mots de passes ne correspondent pas, vérifiez vos entrées.";
        }
        
        if ($passwordHash == $passInDb)
        {
          $editPasswordErrors[] = "Vous utilisez déjà ce mot de passe.";
        }
    
        //No errors, insert into db
        if (empty($editPasswordErrors) && $checkPassword = true)
        {
            $user->editUserPassword($userId, $password);
            $editPasswordSuccess = "Votre mot de passe a été modifié !";
            $_SESSION['tmp'] = ['editPasswordSuccess'=>$editPasswordSuccess];
        }
        else{
            $_SESSION['tmp'] = ['editPasswordError'=>$editPasswordErrors];
        }
        die(var_dump($_SESSION['tmp'],$editPasswordErrors,$editPasswordSuccess));
        header('Location:'.$_SESSION['routes']['account']);
    }
}