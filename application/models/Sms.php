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
     * @param $uid
     * @param $contents
     * @return bool
     */
    public function send($uid,$contents){
        $query = $this->_db->prepare("select `mobile` from `user` where `id` = ?");
        $query->execute(array($uid));
        $ret = $query->fetchAll();
        if(!$ret || count($ret)!=1){
            $this->errno = -4003;
            $this->errmsg = "用户手机号信息查找失败";
        }

        $userMobile = $ret['0']['mobile'];
        if( !$userMobile || !is_numeric($userMobile)||strlen($userMobile)!=11 ){
            $this->errno = -4004;
            $this->errmsg= "手机号不符合规定";
        }

        $code = rand(1000,9999);
        $result = Sms::sendSms($userMobile,$code);

        if($result->Code == "OK"){
            $query = $this->_db->prepare("insert into `sms_record` (`uid`,`contents`) values(?,?)");
            $ret   = $query->execute(array($uid,json_encode(array('code'=>$code))));
            if(!$ret){
                $this->errno = -4006;
                $this->errmsg= "消息发送成功，数据插入失败";
                return false;
            }
            return true;
        } else {
            $this->errno = -4005;
            $this->errmsg= "发送失败".$result->Code;
            return false;
        }
        return true;

    }
}