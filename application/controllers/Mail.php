<?php
/**
 * @name MailController
 * @author joe
 * @desc 邮箱
 */
class MailController extends Yaf_Controller_Abstract {

    public function sendAction(){
        $submit = Common_Request::getRequest("submit","0");
        if($submit != "1"){
            echo Common_Request::response(-3001,"请通过正常渠道提交");
            return false;
        }

        $uid      = Common_Request::postRequest("uid",false);
        $title    = Common_Request::postRequest("title", false);
        $contents = Common_Request::postRequest("contents", false);

        if( !$uid || !$title || !$contents ){
            echo Common_Request::response(-3002,"用户id，邮件title，邮件内容不能为空");
            return false;
        }

        $model = new MailModel();
        if($model->send(intval($uid),trim($title), trim($contents))){
            echo Common_Request::response();
        } else {
            echo Common_Request::response($model->errno, $model->errmsg);
            return false;
        }
        return false;
    }
}