<?php
define('APP',__DIR__);
include APP.'/Joe/Loader.php';

spl_autoload_register('\\Joe\\Loader::autoload');
//Joe\Object::test();
//App\Controller\Home\Index::test();


$obj = new Joe\Object();
$obj->title = 'he';

echo $obj->title;


$obj::zz("123","hello");
echo $obj;