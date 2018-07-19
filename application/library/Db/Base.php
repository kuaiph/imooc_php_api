<?php


class Db_Base{
    public static $_db = null;

    public static function getDb(){
        if(self::$_db == null){
            self::$_db = new PDO("mysql:host=localhost;dbname=imooc_yaf;","root","root");
        }
        return self::$_db;
    }
}