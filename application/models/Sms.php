<?php
/**
 * @name SmslModel
 * @desc smsmodel类
 * @author Joe
 *
 */

require APP . '/application/common/lib/ali/Sms.php';
use app\common\lib\ali\Sms;

class SmsModel{
    public $errno  = 0;
    public $errmsg = "";
    private $_db   = null;

    public function __construct(){
        $this->_db = new PDO("mysql:host=localhost;dbname=imooc_yaf;","root","root");
    }

    /**
     * 验证码发送
     * @param int $uid  用户ID
     * @param string $contents  内容
     * @return bool
     */
    public function send($uid,$contents){
        $query = $this->_db->prepare("select `mobile` from `user` where `id` = ?");
        $query->execute(array($uid));
        $ret = $query->fetchAll();
        if(!$ret || count($ret)!=1){
            list($this->errno,$this->errmsg) = Err_Map::get(-4003);
        }

        $userMobile = $ret['0']['mobile'];
        if( !$userMobile || !is_numeric($userMobile)||strlen($userMobile)!=11 ){
            list($this->errno,$this->errmsg) = Err_Map::get(-4003);
        }

        $code = rand(1000,9999);
        $result = Sms::sendSms($userMobile,$code);

        if($result->Code == "OK"){
            $query = $this->_db->prepare("insert into `sms_record` (`uid`,`contents`) values(?,?)");
            $ret   = $query->execute(array($uid,json_encode(array('code'=>$code))));
            if(!$ret){
                list($this->errno,$this->errmsg) = Err_Map::get(-4006);
                return false;
            }
            return true;
        } else {
            list($this->errno,$this->errmsg) = Err_Map::get(-4005);
            $this->errmsg .= $result->Code;
            return false;
        }
        return true;

    }
}