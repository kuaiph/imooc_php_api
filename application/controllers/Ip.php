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
        $ip = $this->getRequest()->getQuery("ip","");
        if(!$ip || !filter_var($ip, FILTER_VALIDATE_IP)){
            echo json_encode(
                array(
                    "errno"  => -5001,
                    "errmsg" => "IP地址不正确",
                ));
            return false;
        }
        //调用model查询ip归属地
        $model = new IpModel();
        if($data = $model->get(trim($ip))){
            echo json_encode(
                array(
                    "errno"  => 0,
                    "errmsg" => "",
                    "data"   => $data,
                ));
        } else {
            echo json_encode(
                array(
                    "errno"  => $model->errno,
                    "errmsg" => $model->errmsg,
                ));
        }
        return false;
    }
}