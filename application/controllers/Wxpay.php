<?php
/**
 * @name WxpayController
 * @author Joe
 * @desc  微信支付
 */


$qrcodeLibPath = dirname(__FILE__).'/../library/ThirdParty/WxPay/lib/phpqrcode/';
include_once($qrcodeLibPath.'phpqrcode.php');

class WxpayController extends Yaf_Controller_Abstract{

    public function indexAction(){

    }

    //生成订单
    public function createbillAction(){
        $itemId = $this->getRequest()->getQuery("itemid","false");
        if(!$itemId){
            echo json_encode(
                array(
                    "errno"  => -6001,
                    "errmsg" => "请传递正确的商品ID",
                ));
            return false;
        }

        //检查登录
        session_start();
        if(!isset($_SESSION['user_token_time']) || !isset($_SESSION['user_token']) || !isset($_SESSION['user_id'])
        || md5("salt".$_SESSION['user_token_time'].$_SESSION['user_id']) != $_SESSION['user_token']){
            echo json_encode(
                array(
                    "errno"  => -6002,
                    "errmsg" => "请先登录后操作",
                ));
            return false;
        }
        $model = new WxpayModel();
        if( $data = $model->createbill($itemId, $_SESSION['user_id']) ){
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


    /**
     * 生成二维码
     */
    public function qrcodeAction(){
        $billId = $this->getRequest()->getQuery("billid","");
        if(!$billId){
            echo json_encode(
                array(
                    "errno"  => -6009,
                    "errmsg" => "请传递正确的订单ID",
                ));
            return false;
        }

        $model = new WxpayModel();
        if($data = $model->qrcode($billId)){
            QRcode::png($data);
        } else {
            echo json_encode(
                array(
                    "errno"  => $model->errno,
                    "errmsg" => $model->errmsg,
                ));
        }

        return true;
    }

    public function callbackAction(){
        $model = new WxpayModel();
        $model->callback();
        echo json_encode(
            array(
                "errno"  => 0,
                "errmsg" => "",
            ));

        return true;
    }
}