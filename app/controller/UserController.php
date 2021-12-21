<?php

require_once './app/model/UserManager.php';

class UserController{

    public function userList($page){
        global $twig;
        unset($_SESSION['tmp']);
        
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
        global $router;
        unset($_SESSION['tmp']);
        $user = new UserManager();
        $user->setAdmin($userId);
        $username = $user->getUsername(implode($userId));
        $_SESSION['tmp'] = 'Les droits administrateur ont été accordés à <strong>'.$username.'</strong>.';
        header('Location:'.$router->generate('userlist').'1');
    }   
    
    public function unsetUserAdmin($userId){
        global $router;
        unset($_SESSION['tmp']);
        $user = new UserManager();
        $user->unsetAdmin($userId);
        $username = $user->getUsername(implode($userId));
        $_SESSION['tmp'] = 'Les droits administrateur de <strong>'.$username.'</strong> ont été révoqués';
        header('Location:'.$router->generate('userlist').'1');
    }

    public function setUserActive($userId){
        global $router;
        unset($_SESSION['tmp']);
        $user = new UserManager();
        $user->setActive($userId);
        $username = $user->getUsername(implode($userId));
        $_SESSION['tmp'] = 'L\'utilisateur <strong>'.$username.'</strong> a été activé';
        header('Location:'.$router->generate('userlist').'1');
    }

    public function setUserInactive($userId){
        global $router;
        unset($_SESSION['tmp']);
        $user = new UserManager();
        $user->setInactive($userId);
        $username = $user->getUsername(implode($userId));
        $_SESSION['tmp'] = 'L\'utilisateur <strong>'.$username.'</strong> a été désactivé';
        header('Location:'.$router->generate('userlist').'1');
    }

    public function deleteUser($userId){
        global $router;
        unset($_SESSION['tmp']);
        $user = new UserManager();
        $username = $user->getUsername(implode($userId));
        $_SESSION['tmp'] = 'L\'utilisateur <strong>'.$username.'</strong> a été supprimé';
        $user->userDelete($userId);
        header('Location:'.$router->generate('userlist').'1');
    }

    public function myAccount(){
        global $router,$twig;
        unset($_SESSION['tmp']);
        $user = new UserManager();
        if(isset($_SESSION['userId'])){
            $userId = $_SESSION['userId'];
            $currentUser = $user->getUserInfos($userId);
            echo $twig->render('myaccount.twig', ['currentUser'=>$currentUser]);
        }
        else{
            header('Location:'.$router->generate('home'));
        }
        
    }

    public function editPassword($userId){
        global $router, $twig;
        unset($_SESSION['tmp']);

        // GETTING CURRENT USER INFOS
        $user = new UserManager();
        $userId = implode($userId);
        $currentUser = $user->getUserInfos($userId);
        
        // SETTING SUCCESS/ERRORS ARRAYS
        $editPasswordErrors = [];
        $editPasswordSuccess = [];

        //CHECKING INPUTS
        if (empty($_POST['oldPassword'])){
            $editPasswordErrors[] = "Merci de renseigner votre mot de passe actuel";
        }
        if (empty($_POST['password'])){
            $editPasswordErrors[] = "Merci de renseigner un nouveau mot de passe";
        }
        if (empty($_POST['passwordCheck'])){
            $editPasswordErrors[] = "Merci de renseigner la validation du mot de passe.";
        }

        //ALL INPUTS COMPLETED
        if (isset($_POST['password']) && !empty($_POST['password']) && isset($_POST['passwordCheck']) && !empty($_POST['passwordCheck']) && isset($_POST['oldPassword']) && !empty($_POST['oldPassword'])){
            $oldPassword = $_POST['oldPassword'];
            $password = $_POST['password'];
            $passwordCheck = $_POST['passwordCheck'];

            $passInDb = $currentUser['userPassword'];
            $oldPasswordHash = sha1($oldPassword);
            $checkOldPassword = $oldPasswordHash === $passInDb;
            $checkPasswords = $password === $passwordCheck;
            $passwordHash = sha1($password);

            if (!$checkPasswords){
                $editPasswordErrors[] = "Les mots de passes ne correspondent pas, vérifiez vos entrées.";
            }
            if (!$checkOldPassword){
                $editPasswordErrors[] = "Mot de passe actuel erroné.";
            }
        }

        //NO ERROR -- INSERT INTO DB
        if (empty($editPasswordErrors) && $checkPasswords && $checkOldPassword){
            $user->editUserPassword($userId, $passwordHash);
            $editPasswordSuccess = "Votre mot de passe a été modifié.";
            $_SESSION['tmp'] = ['loginSuccess'=>$editPasswordSuccess];
            
            // DISCONNECTING USER
            unset($_SESSION['username'], $_SESSION['userIsAdmin'], $_SESSION['userId']);
            header('Location:'.$router->generate('authentication'));
        }
        else{
            $_SESSION['tmp'] = ['editPasswordError'=>$editPasswordErrors];
            print("<script type=\"text/javascript\">setTimeout('location=(".$router->generate('authentication').")' ,1000);</script>");
        }

    }

    public function userIsAdmin($username){
        getUserIsAdmin($username);
    }

    public function userIsActive($username){
        getUserIsActive($username);
    }
    
}