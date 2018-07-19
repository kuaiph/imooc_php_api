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

    private $_dao = null;

    public function __construct(){
        $this->_dao = new Db_User();
    }
    /**
     * 注册
     * @param $uname
     * @param $pwd
     * @return bool
     */
    public function register($uname, $pwd){
        if(!$this->_dao->checkExists($uname)){
            $this->errno = $this->_dao->errno();
            $this->errmsg= $this->_dao->errmsg();
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

        if(!$this->_dao->addUser($uname,$password,date("Y-m-d H:i:s"))){
            $this->errno = $this->_dao->errno();
            $this->errmsg= $this->_dao->errmsg();
            return false;
        }
        return true;
    }

    public function login($uname, $pwd) {
        $userInfo = $this->_dao->find($uname);
        if(!$userInfo){
            $this->errno = $this->_dao->errno();
            $this->errmsg= $this->_dao->errmsg();
        }
        if( $userInfo['pwd'] != Common_Password::pwdEncode($pwd) ) {
            $this->errno = -1004;
            $this->errmsg= "密码错误";
            return false;
        }
        return intval($userInfo[1]);
    }


}