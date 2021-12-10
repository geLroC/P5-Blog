<?php

class Dbconnect{
    public static function connect(){
        
        $data = require __DIR__.'/../config/dbconfig.php';

        try{
            $database = new PDO('mysql:host='.$data['host'].';dbname='.$data['dbname'].';charset=utf8',$data['username'],$data['password']);
            $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $database;
        }
        catch (Exception $err){
            echo 'Erreur : '.$err->getMessage();
        }
    }
}