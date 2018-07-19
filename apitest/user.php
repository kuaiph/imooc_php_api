<?php

require_once __DIR__."/../vendor/autoload.php";

use Curl\Curl;


$host = "http://imooc-yaf:8888";

$curl = new Curl();
$username = 'apitest_uname_'.rand();
$password = 'apitest_pwd_'.rand();

/**
 * 注册接口验证
 */

$curl->post('/user/register',
            array(
                "uname"=>$username,
                "pwd"  =>$password,
            ));

if($curl->error){
    die("Error".$curl->error_code.": ErrorMesage".$curl->error_message."\n");
}else{
    $resp = json_decode($curl->response,true);
    if($resp['errno'] != 0){
        die("接口调用失败:".$resp['errmsg']."\n");
    }
    echo "注册成功-".$username."\n";
}


/**
 * 登录接口验证
 */

$curl->post('/user/login',
            array(
                "uname"=>$username,
                "pwd"  =>$password,
            ));


if($curl->error){
    die("Error".$curl->error_code.": ErrorMesage".$curl->error_message."\n");
}else{
    $resp = json_decode($curl->response,true);
    if($resp['errno'] != 0){
        die("登录调用失败:".$resp['errmsg']."\n");
    }
    echo "登录成功-".$username."\n";
}


echo "check done\n";