<?php

/**
 * @desc 用户数据库操作
 */
class Db_User extends Db_Base {
    public function find($uname){
        $query = self::getDb()->prepare("select `pwd`, `id` from `user` where `name` = ? ");
        $query->execute(array($uname));
        $ret   = $query->fetchAll();
        if( !$ret || count($ret)!=1 ) {
            list(self::$errno,self::$errmsg) = Err_Map::get(1003);
            return false;
        }
        return $ret[0];
    }

    public function checkExists($uname){
        $query = self::getDb()->prepare("select count(*) as c from `user` where `name` = ?");
        $query->execute(array($uname));
        $count   = $query->fetchAll();
        if( $count[0]['c'] !=0 ) {
            list(self::$errno, self::$errmsg) = Err_Map::get(1005);
            return false;
        }
        return true;
    }


    public function addUser($uname,$password,$datetime){
        $query = self::getDb()->prepare("insert into `user` (`id`,`name`,`pwd`,`reg_time`) VALUES (null,?,?,?)");
        $ret = $query->execute(array($uname, $password,$datetime));
        if( !$ret ) {
            list(self::$errno,self::$errmsg) = Err_Map::get(1007);
            return false;
        }
        return true;
    }
}
