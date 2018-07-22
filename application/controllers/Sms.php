<?php
/**
 * @name SmsController
 * @author joe
 * @desc 短信处理
 */






class SmsController extends Yaf_Controller_Abstract {
    public function indexAction(){

    }

    /**
     * 发送短信
     * @return bool
     */
    public function sendAction(){
        $submit = Common_Request::getRequest("submit","0");
        if($submit != "1"){
            echo Common_Request::response(-4001);
            return false;
        }

        $uid      = Common_Request::postRequest("uid", false);
        $contents = Common_Request::postRequest("contents",false);
        if(!$uid || !$contents){
            echo Common_Request::response(-4002);
            return false;
        }
        $model = new SmsModel();
        if( $model->send(intval($uid), trim($contents)) ){
            echo Common_Request::response();
        } else {
            echo Common_Request::response($model->errno,$model->errmsg);

        }
        return false;
    }
}