<?php
/**
 * @name MailModel
 * @desc 邮箱model类
 * @author Joe
 *
 */
require __DIR__ . "/../../vendor/autoload.php";
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;

class MailModel{
    public $errno  = 0;
    public $errmsg = "";
    private $_db   = null;

    public function __construct(){
        $this->_db = new PDO("mysql:host=localhost;dbname=imooc_yaf;","root","root");
    }

    /**
     * 邮箱发送
     * @param $uid
     * @param $title
     * @param $contents
     * @return bool
     */
    public function send($uid,$title,$contents){

        $query = $this->_db->prepare("select `email` from `user` where `id` = ?");
        $query->execute(array(intval($uid)));
        $ret = $query->fetchAll();
        if(!$ret || count($ret) != 1){
            list($this->errno,$this->errmsg) = Err_Map::get(-3003);
            return false;
        }

        $userEmail = $ret[0]['email'];
        if( !filter_var($userEmail, FILTER_VALIDATE_EMAIL)){
            list($this->errno,$this->errmsg) = Err_Map::get(-3004);
            return false;
        }

        $mail = new Message();
        $mail->setFrom('joe <joewttx@126.com>')
            ->addTo($userEmail)
            ->setSubject($title)
            ->setBody($contents);
        //填写自己的邮箱信息
        $mailer = new SmtpMailer([
            "host"     => 'smtp.126.com',
            "username" => 'joewttx@126.com',
            "password" => 'xxxxx',
            "secure"   => 'ssl',
        ]);
        $resp = $mailer->send($mail);
        return true;
    }
}