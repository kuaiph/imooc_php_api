<?php

class Common_Password{
    const SALT = "joe";
    /**
     * 密码生成
     * @param $pwd
     * @return string
     */
    public static function pwdEncode($pwd)
    {
        return md5(self::SALT.$pwd);
    }
}