<?php
require_once 'app/class/Dbconnect.php';

class UserManager{

    public function getUserIsAdmin(){
    	$username = $_POST['username'];
    	$req = DbConnect::connect()->prepare('SELECT userIsAdmin 
		FROM user 
		WHERE userName = ?');
    	$req->execute([$username]);
    	$res = $req->fetchColumn();
    	return $res;
    }

    public function getUserIsActive(){
		$username = $_POST['username'];
    	$req = DbConnect::connect()->prepare('SELECT userIsActive 
		FROM user 
		WHERE userName = ?');
    	$req->execute([$username]);
    	$res = $req->fetchColumn();
    	return $res;
    }

    public function getUserId($username){
    	$username = $_POST['username'];
    	$req = DbConnect::connect()->prepare('SELECT userId 
		FROM user 
		WHERE userName = ?');
    	$req->execute([$username]);
    	$res = $req->fetchColumn();
    	return $res;
    }

    public function getUsername($userid){
		$req = DbConnect::connect()->prepare('SELECT userName 
		FROM user 
		WHERE userId = ?');
    	$req->execute([$userid]);
    	$res = $req->fetchColumn();
    	return $res;
    }

    public function getUserInfos($userId){
    	$req = DbConnect::connect()->prepare('SELECT userId, userName, userMail, userPassword, DATE_FORMAT(userCreationDate, \'%d/%m/%Y à %Hh%i\') AS userCreationDateFr, userIsAdmin 
		FROM user 
		WHERE userId = ?');
		$req->execute([$userId]);
    	$res = $req->fetch();
    	return $res;
    }

    public function getUserList(){
    	$userList = DbConnect::connect()->query('SELECT userId, userMail, userName, DATE_FORMAT(userCreationDate, \'%d/%m/%Y à %Hh%i\') AS userCreationDateFr, userIsAdmin, userIsActive 
		FROM user 
		ORDER BY userName');
		$users = $userList->fetchAll(PDO::FETCH_ASSOC);
		die(var_dump($userList, $users));
    	return $users;
    }

    public function setAdmin($userId){
		$userId = implode($userId);
    	$req = DbConnect::connect()->prepare('UPDATE user 
		SET userIsAdmin = 1 
		WHERE userId = :userId');
    	$res = $req->execute(["userId"=>$userId]);
    }

	public function unsetAdmin($userId){
		$userId = implode($userId);
    	$req = DbConnect::connect()->prepare('UPDATE user 
		SET userIsAdmin = 0 
		WHERE userId = :userId');
    	$res = $req->execute(["userId"=>$userId]);
    }

    public function setActive($userId){
    	$userId = implode($userId);
    	$req = DbConnect::connect()->prepare('UPDATE user 
		SET userIsActive = 1 
		WHERE userId = :userId');
    	$res = $req->execute(["userId"=>$userId]);
    	return $res;
    }    

	public function setInactive($userId){
    	$userId = implode($userId);
    	$req = DbConnect::connect()->prepare('UPDATE user 
		SET userIsActive = 0 
		WHERE userId = :userId');
    	$res = $req->execute(["userId"=>$userId]);
    	return $res;
    }

    public function userDelete($userId){
		$userId = implode($userId);
    	$deleteUser = DbConnect::connect()->prepare('UPDATE user 
		SET userName = "Utilisateur supprimé", userIsActive = null, userIsAdmin = null 
		WHERE userId = ?');
		$deleteUser->execute([$userId]);
    }

    public function registerUser($usermail, $username, $password){
    	$req = DbConnect::connect()->prepare('INSERT INTO user(userMail, userName, userPassword, userIsActive) 
		VALUES (:usermail, :username, :password, 1)');
    	$req->execute(array('usermail' => $usermail, 'username' => $username, 'password' => $password));
    }

    public function checkUsername($username){
    	$username = $_POST['username'];
    	$checkUsername = DbConnect::connect()->prepare('SELECT userName 
		FROM user 
		WHERE userName = ?');
    	$checkUsername->execute([$username]);
    	$user = $checkUsername->fetchColumn();
    	return $user;
    }

    public function checkPassword($password){
    	$username = $_POST['username'];
    	$passwordCheck = DbConnect::connect()->prepare('SELECT userPassword 
		FROM user 
		where userName = ?');
    	$passwordCheck->execute([$username]);
    	$passwordInDb = $passwordCheck->fetch();
    	$passwordIsCorrect = $password == $passwordInDb['userPassword'];
    	return $passwordIsCorrect;
    }

    public function checkUsermail($usermail){
    	$usermailCheck = DbConnect::connect()->prepare('SELECT userMail 
		FROM user 
		WHERE userMail = ?');
    	$usermailCheck->execute([$usermail]);
    	$usermail = $usermailCheck->fetch();
    	return $usermail;
    }

    public function checkUser($username, $password){
    	$req = DbConnect::connect()->prepare('SELECT userName, userPassword 
		FROM user 
		WHERE userName = :userName');
    	$req->execute(["userName"=>$username]);
    	$res = $req->fetch();
		if ($res === false){
			return $userOK = false;
		}
		else{
    	$userOK = ($username === $res['userName'] && $password === $res['userPassword']);
    	return $userOK;
		}
    }

    public function editUserPassword($userId, $userpassword){
    	$userpassword = sha1($_POST['password']);
    	$req = DbConnect::connect()->prepare('UPDATE user 
		SET userPassword = :userPassword 
		WHERE userId = :userId');
    	$req->execute(array('userPassword'=>$userpassword, 'userId'=>$userId));
    }

	public function getPaginUserList($page, $usersByPage){
		$user = self::userCount();
		$start = ($page-1)*$usersByPage;
		$userList = DbConnect::connect()->query('SELECT userId, userMail, userName, DATE_FORMAT(userCreationDate, \'%d/%m/%Y à %Hh%i\') AS userCreationDateFr, userIsAdmin, userIsActive 
		FROM user 
		WHERE userName != "Utilisateur supprimé"
		ORDER BY userName ASC
		LIMIT '.$start.','.$usersByPage);
		$users = $userList->fetchAll(PDO::FETCH_ASSOC);
		return $users;
	}

	public function userCount(){
		$userCount = DbConnect::connect()->query('SELECT userId 
		FROM user 
		WHERE userName != "Utilisateur supprimé"');
		$res = $userCount->rowCount();
		return $res;
	}

}