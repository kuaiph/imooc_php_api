<?php
/**
 * @name SmsController
 * @author joe
 * @desc 短信处理
 */






class SmsController extends Yaf_Controller_Abstract {
    public function indexAction(){

    }

    public function sendAction(){
        $submit = $this->getRequest()->getQuery("submit","0");
        if($submit != "1"){
            echo json_encode(
                array(
                    "errno"  => -4001,
                    "errmsg" => "请通过正常渠道提交",
                ));
            return false;
        }

        $uid      = $this->getRequest()->getPost("uid",false);
        $contents = $this->getRequest()->getPost("contents",false);
        if(!$uid || !$contents){
            echo json_encode(
                array(
                    "errno"  => -4002,
                    "errmsg" => "用户Id，短信内容不能为空",
                ));
            return false;
        }
        $model = new SmsModel();
        if( $model->send(intval($uid), trim($contents)) ){
            echo json_encode(
                array(
                    "errno"  => 0,
                    "errmsg" => "",
                ));
        } else {
            echo json_encode(
                array(
                    "errno" => $model->errno,
                    "errmsg"=> $model->errmsg,
                ));

        }
        return false;
    }
}