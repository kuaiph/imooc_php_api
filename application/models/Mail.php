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
            $this->errno = -3003;
            $this->errmsg = "邮箱信息查找失败";
            return false;
        }

        $userEmail = $ret[0]['email'];
        if( !filter_var($userEmail, FILTER_VALIDATE_EMAIL)){
            $this->errno = -3004;
            $this->errmsg = "邮箱不合法".$userEmail;
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