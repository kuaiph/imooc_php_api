<?php


require_once __DIR__ . '/../../vendor/autoload.php';
class IpModel{
    public $errno = 0;
    public $errmsg = "";
    private $bs = null;
    public function __construct(){
        $this->bs = new ipip\datx\City(__DIR__.'/../../application/library/ThirdParty/17monipdb/17monipdb.datx');

    }
    //ip地址转详细地址 使用了 ipip.net
    public function get($ip){
        $rep = $this->bs->find($ip);
        return $rep;
    }
}