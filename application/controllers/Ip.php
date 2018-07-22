<?php

/**
 * @name IpController
 * @desc ip地址查询
 * @author Joe
 */


class IpController extends Yaf_Controller_Abstract {
    public function indexAction(){

    }

    public function getAction(){
        $ip = Common_Request::getRequest("ip");
        if(!$ip || !filter_var($ip, FILTER_VALIDATE_IP)){
            echo Common_Request::response(-5001,"IP地址不正确");
            return false;
        }
        //调用model查询ip归属地
        $model = new IpModel();
        if($data = $model->get(trim($ip))){
            echo Common_Request::response(0,'',$data);
        } else {
            echo Common_Request::response($model->errno, $model->errmsg);
        }
        return false;
    }
}