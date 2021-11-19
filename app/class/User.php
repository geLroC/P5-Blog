<?php

class User
{
    protected $_userName;
    protected $_userPassword;
    protected $_userMail;
    protected $_userIsAdmin;
    protected $_userIsActive;

    public function __construct($userMail, $userName, $userPassword){
        $this->_userName = $userName;
        $this->_userMail = $userMail;
        $this->_userPassword = $userPassword;
    }

    //Setters
    public function setUserName($userName){
        if(is_string($userName))
        {
            $this->_userName = $userName;
        }
    }
    
    public function setUserPassword($userPassword){
        if(is_string($userPassword))
        {
            $this->_userPassword = $userPassword;
        }
    }

    public function setUserMail($userMail){
        if(is_string($userMail))
        {
            $this->_userMail = $userMail;
        }
    }

    public function setUserIsAdmin($userIsAdmin){
    }

    public function setUserIsActive($userIsActive){
    }

    //Getters
    public function getUserName(){
        return $this->_userName;
    }

    public function getUserPassword(){
        return $this->_userPassword;
    }

    public function getUserMail(){
        return $this->_userMail;
    }

    public function getUserIsAdmin(){
        return $this->_userIsAdmin;
    }

    public function getUserIsActive(){
        return $this->_userIsActive;
    }
}

