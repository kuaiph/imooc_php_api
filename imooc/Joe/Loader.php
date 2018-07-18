<?php
namespace Joe;

class Loader
{
    static function autoload($class)
    {
        $file = APP.'/'.str_replace('\\','/',$class).'.php';
        require $file;
    }
}