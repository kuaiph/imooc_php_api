<?php
namespace Joe;

class Object
{

    protected $title=array();
    public static function test()
    {
        echo __DIR__."\n";
    }


    public function __get($key)
    {

        return $this->title[$key];
    }


    public function __set($key, $value)
    {
        $this->title[$key] = $value;
    }

    public function __call($func, $param)
    {
        var_dump($func,$param);
    }


    public static function __callStatic($func, $param)
    {
        var_dump($func,$param);
    }


    public function __toString()
    {
        // TODO: Implement __toString() method.
        return __CLASS__;
    }
}