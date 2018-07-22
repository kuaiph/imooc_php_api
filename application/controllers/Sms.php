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
        $submit = Common_Request::getRequest("submit","0");
        if($submit != "1"){
            echo Common_Request::response(-4001,"请通过正常渠道提交");
            return false;
        }

        $uid      = Common_Request::postRequest("uid", false);
        $contents = Common_Request::postRequest("contents",false);
        if(!$uid || !$contents){
            echo Common_Request::response(-4002,"用户Id，短信内容不能为空");
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