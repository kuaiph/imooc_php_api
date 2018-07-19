<?php

/**
 * @name UserModel
 * @desc 用户操作model类
 * @author joe
 */
class UserModel{

    public $errno  = 0;
    public $errmsg = "";
    //protected static $_db   = null;

    private $_db = null;

    public function __construct(){
        $this->_db = new PDO("mysql:host=localhost;dbname=imooc_yaf;","root","root");
    }

    /**
     * 单例模式
     */
    static function getInstance(){
        if( self::$_db ) {
            return self::$_db;
        } else {
            self::$_db = new self();
            return self::$_db;
        }
    }

    /**
     * 注册
     * @param $uname
     * @param $pwd
     * @return bool
     */
    public function register($uname, $pwd){
        $query = $this->_db->prepare("select count(*) as c from `user` where `name` = ?");
        $query->execute(array($uname));
        $count = $query->fetchAll();


        if ($count[0]['c'] != 0){
            $this->errno = -1005;
            $this->errmsg = "用户名已存在";
            return false;
        }

        $password = "";
        if( strlen($pwd) < 8 ){
            $this->errno = -1006;
            $this->errmsg = "密码太短，请输入最低8位的密码";
            return false;
        } else {
            $password = Common_Password::pwdEncode($pwd);
        }

        $query = $this->_db->prepare("insert into `user` (`id`,`name`,`pwd`,`reg_time`) VALUES (null,?,?,?)");
        $ret = $query->execute(array($uname, $password,date("Y-m-d H:i:s")));
        if( !$ret ) {
            $this->errno = -1006;
            $this->errmsg = "注册失败，写入数据库失败";
            return false;
        }
        return true;
    }

    public function login($uname, $pwd) {
        $query = $this->_db->prepare("select `pwd`, `id` from `user` where `name` = ? ");
        $query->execute(array($uname));
        $ret   = $query->fetchAll();
        if( !$ret || count($ret)!=1 ) {
            $this->errno = -1003;
            $this->errmsg= "用户查找失败";
            return false;
        }
        $userInfo = $ret[0];
        if( $userInfo['pwd'] != Common_Password::pwdEncode($pwd) ) {
            $this->errno = -1004;
            $this->errmsg= "密码错误";
            return false;
        }
        return intval($userInfo[1]);
    }


}