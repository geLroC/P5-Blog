<?php

require_once './app/model/UserManager.php';

class UserController{

    public function userList($page){
        global $twig;
        
        //PREPARIONG PAGINATION
        $user = new userManager();
        $usersByPage = 5;
        $nbusers = $user->userCount();
        $totalPages = ceil($nbusers/$usersByPage);
        $page = implode($page);
        if(!isset($page) || $page > $totalPages || $page <= 0){
            $page = 1;
            header('Location:'.$_SESSION['routes']['userlist'].$page);
        }
        //PAGINATION READY
        $users = $user->getPaginUserList($page, $usersByPage);
        echo $twig->render('userlist.twig', array_merge(['userlist'=>$users, 'page'=>$page, 'usersbypage'=>$usersByPage, 'totalpages'=>$totalPages]));
    }
    
    public function setUserAdmin($userId){
        unset($_SESSION['tmp']);
        $user = new UserManager();
        $user->setAdmin($userId);
        $username = $user->getUsername(implode($userId));
        $_SESSION['tmp'] = 'Les droits administrateur ont été accordés à <strong>'.$username.'</strong>.';
        header('Location:'.$_SESSION['routes']['userlist'].'1');
    }   
    
    public function unsetUserAdmin($userId){
        unset($_SESSION['tmp']);
        $user = new UserManager();
        $user->unsetAdmin($userId);
        $username = $user->getUsername(implode($userId));
        $_SESSION['tmp'] = 'Les droits administrateur de <strong>'.$username.'</strong> ont été révoqués';
        header('Location:'.$_SESSION['routes']['userlist'].'1');
    }

    public function setUserActive($userId){
        unset($_SESSION['tmp']);
        $user = new UserManager();
        $user->setActive($userId);
        $username = $user->getUsername(implode($userId));
        $_SESSION['tmp'] = 'L\'utilisateur <strong>'.$username.'</strong> a été activé';
        header('Location:'.$_SESSION['routes']['userlist'].'1');
    }

    public function setUserInactive($userId){
        unset($_SESSION['tmp']);
        $user = new UserManager();
        $user->setInactive($userId);
        $username = $user->getUsername(implode($userId));
        $_SESSION['tmp'] = 'L\'utilisateur <strong>'.$username.'</strong> a été désactivé';
        header('Location:'.$_SESSION['routes']['userlist'].'1');
    }

    public function deleteUser($userId){
        unset($_SESSION['tmp']);
        $user = new UserManager();
        $username = $user->getUsername(implode($userId));
        $_SESSION['tmp'] = 'L\'utilisateur <strong>'.$username.'</strong> a été supprimé';
        $user->userDelete($userId);
        header('Location:'.$_SESSION['routes']['userlist']);
    }

    public function myAccount(){
        global $twig;
        unset($_SESSION['tmp']);
        $user = new UserManager();
        if(isset($_SESSION['userId'])){
            $currentUser = $user->getUserInfos($_SESSION['userId']);
            echo $twig->render('myaccount.twig', ['currentUser'=>$currentUser]);
        }
        else{
            header('Location:'.$_SESSION['routes']['home']);
        }
        
    }

    public function editPassword($userId){
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
    
        //CHECKING INPUTS
        if (!isset($password) || empty ($password)){
            $editPasswordErrors[] = "Merci de renseigner un nouveau mot de passe";
        }
        if (!isset($password) || empty ($password)){
            $editPasswordErrors[] = "Merci de renseigner la validation du mot de passe.";
        }
        if (!empty($password) && !$checkPasswords){
            $editPasswordErrors[] = "Les mots de passes ne correspondent pas, vérifiez vos entrées.";
        }
        
        if ($passwordHash == $passInDb){
            $editPasswordErrors[] = "Vous utilisez déjà ce mot de passe.";
        }
    
        //NO ERROR -- INSERT INTO DB
        if (empty($editPasswordErrors) && $checkPassword = true){
            $user->editUserPassword($userId, $password);
            $editPasswordSuccess = "Votre mot de passe a été modifié !";
            $_SESSION['tmp'] = ['editPasswordSuccess'=>$editPasswordSuccess];
        }
        else{
            $_SESSION['tmp'] = ['editPasswordError'=>$editPasswordErrors];
        }
        header('Location:'.$_SESSION['routes']['account']);
    }

    public function userIsAdmin(){
        getUserIsAdmin();
    }

    public function userIsActive(){
        getUserIsActive();
    }
    
}