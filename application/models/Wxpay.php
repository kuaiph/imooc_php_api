<?php

/**
 * @name WxpayModel
 * @desc 微信支付
 * @author joe
 */

$wxpayLibPath = dirname(__FILE__).'/../library/ThirdParty/WxPay/lib/';
include_once($wxpayLibPath.'WxPay.Api.php');
include_once($wxpayLibPath.'WxPay.Notify.php');
include_once($wxpayLibPath.'WxPay.Data.php');
include_once($wxpayLibPath.'WxPay.NativePay.php');

class WxpayModel extends WxPayNotify {
    public $errno = 0;
    public $errmsg = "";
    private $_db = null;

    public function __construct()
    {
        $this->_db = new PDO("mysql:host=localhost;dbname=imooc_yaf;","root","root");
    }

    /**
     * 创建订单
     * @param $itemid
     * @param $uid
     * @return bool|int
     */
    public function createbill($itemid, $uid){
        $query = $this->_db->prepare("select * from `item` where `id` = ?");
        $query->execute(array($itemid));
        $ret = $query->fetchAll();
        if( !$ret || count($ret)!=1 ){
            $this->errno  = -6004;
            $this->errmsg = "找不到这件商品";
            return false;
        }

        $item = $ret['0'];
        if(strtotime($item['etime']) <= time()){
            $this->errno  = -6005;
            $this->errmsg = "商品过期";
            return false;
        }
        if(intval($item['stock'])<=0){
            $this->errno  = -6006;
            $this->errmsg = "商品没有库存";
            return false;
        }

        //创建bill

        $query = $this->_db->prepare("insert into `bill` (`itemid`,`uid`,`price`,`status`) VALUES (?,?,?,'unpaid') ");
        $ret = $query->execute(array($itemid, $uid, intval($item['price'])));
        if(!$ret){
            $this->errno  = -6007;
            $this->errmsg = "创建订单失败";
            return false;
        }

        $lastid = intval($this->_db->lastInsertId());

        //更新库存

        $query = $this->_db->prepare("update `item` set `stock`=`stock`-1 where `id`= ?");
        $ret = $query->execute(array($itemid));
        if(!$ret){
            $this->errno  = -6008;
            $this->errmsg = "更新库存失败";
        }

        return $lastid;

    }


    /**
     * 订单二维码生成
     * @param $billid
     * @return mixed
     */
    public function qrcode($billid) {
        $query = $this->_db->prepare("select * from `bill` where `id` = ?");
        $query->execute(array($billid));
        $ret = $query->fetchAll();
        if( !$ret || count($ret)!=1 ){
            $this->errno  = -6011;
            $this->errmsg = "找不到订单信息";
            return false;
        }

        $bill = $ret[0];
        $query = $this->_db->prepare("select * from `item` where `id` = ?");
        $query->execute(array($bill['itemid']));
        $ret = $query->fetchAll();
        if( !$ret || count($ret) !=1 ){
            $this->errno  = -6012;
            $this->errmsg = "找不到商品信息";
            return false;
        }

        $item = $ret[0];

        /*
        $input = new WxPayUnifiedOrder();
        $input->SetBody($item['name']);
        $input->SetAttach($billid);
        // MCHID 商户号
        //$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee($bill['price']);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis",time()+86400));
        $input->SetGoods_tag($item['name']);
        $input->SetNotify_url("http://1.1.1.1/wxpay/callback");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($billid);

        $notify = new NativePay();
        $result = $notify->GetPayUrl($input);
        $url = $result['code_url'];
        */
        $url = "https://joewt.com";

        return $url;
    }

    public function callback(){
        //需要sk 需要企业资质
        $xmlData = file_get_contents("php://input");
        if( substr_count( $xmlData, "<result_code><![CDATA[SUCCESS]]></result_code>" )==1 &&
            substr_count( $xmlData, "<return_code><![CDATA[SUCCESS]]></return_code>" )==1 )
        {
            preg_match( '/<attach>(.*)\[(\d+)\](.*)<\/attach>/i', $xmlData, $match );
            if( isset($match[2])&&is_numeric($match[2]) ) {
                $billId = intval( $match[2] );
            }
            preg_match( '/<transaction_id>(.*)\[(\d+)\](.*)<\/transaction_id>/i', $xmlData, $match );
            if( isset($match[2])&&is_numeric($match[2]) ) {
                $transactionId = intval( $match[2] );
            }
        }
        if( isset($billId) && isset($transactionId) ) {
            $query = $this->_db->prepare("update `bill` set `transaction`=? ,`ptime`=? ,`status`='paid' where `id`=? ");
            $query->execute( array( $transactionId, date("Y-m-d H:i:s"), $billId ) );
        }

    }
}