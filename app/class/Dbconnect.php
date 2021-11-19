<?php

class Dbconnect{
    public static function connect(){
        
        $data = require __DIR__.'/../config/dbconfig.php';

        try{
            $db = new PDO('mysql:host='.$data['host'].';dbname='.$data['dbname'].';charset=utf8',$data['username'],$data['password']);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $db;
        }
        catch (Exception $e){
            die('Erreur : '.$e->getMessage());
        }
    }
}