<?php

require_once './app/model/UserManager.php';

class UserController{

    public function userList($page){
        global $twig;
        
        //Preparing pagination
        $user = new userManager();
        $usersByPage = 5;
        $nbusers = $user->userCount();
        $totalPages = ceil($nbusers/$usersByPage);
        $page = implode($page);
        if(!isset($page) || $page > $totalPages || $page <= 0){
        $page = 1;
        header('Location:'.$_SESSION['routes']['userlist'].$page);
        }
        //Pagination is Ready
        $users = $user->getPaginUserList($page, $usersByPage);
        echo $twig->render('userlist.twig', array_merge(['userlist'=>$users, 'page'=>$page, 'usersbypage'=>$usersByPage, 'totalpages'=>$totalPages]));
    }

    //public function userIsAdmin(){
    //    getUserIsAdmin();
    //}

    //public function userIsActive(){
    //    getUserIsActive();
    //}
    
    public function setUserAdmin($userId){
        $user = new UserManager();
        $user->setAdmin($userId);
        header('Location:'.$_SESSION['routes']['userlist']);
    }   
    
    public function unsetUserAdmin($userId){
        $user = new UserManager();
        $user->unsetAdmin($userId);
        header('Location:'.$_SESSION['routes']['userlist']);
    }

    public function setUserActive($userId){
        $user = new UserManager();
        $user->setActive($userId);
        header('Location:'.$_SESSION['routes']['userlist']);
    }

    public function setUserInactive($userId){
        $user = new UserManager();
        $user->setInactive($userId);
        header('Location:'.$_SESSION['routes']['userlist']);
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
        $currentUser = $user->getUserInfos($_SESSION['userId']);
        echo $twig->render('myaccount.twig', ['currentUser'=>$currentUser]);
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
        header('Location:'.$_SESSION['routes']['account']);
    }
}