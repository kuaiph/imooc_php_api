<?php


class Db_Base{
    public static $errno  = 0;
    public static $errmsg = null;
    public static $db     = null;

    public static function getDb(){
        if(self::$db == null){
            self::$db = new PDO("mysql:host=localhost;dbname=imooc_yaf;","root","root");
            //防止pdo在拼接sql的时候将int转string
            self::$db->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
        }
        return self::$db;
    }

    public function errno(){
        return self::$errno;
    }

    public function errmsg(){
        return self::$errmsg;
    }
}