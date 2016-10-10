<?php
/**
 * 参见 messy.php:
 *   - 在项目中直接调用那些方法, 代码会和子系统偶合在一起
 *   - 当子系统变化, 或我们决定将其与子系统完全断开时, 代码就会出现问题
 *
 * 外观模式的概念十分简单, 他只是为这个子系统创建一个清晰的接口
 */
require "messy.php";

class ProductFacade
{
    private $products = array();

    function __construct($file)
    {
        $this->file = $file;
        $this->compile();
    }

    private function compile()
    {
        $lines = getProductFileLines($this->file);
        foreach ($lines as $line) {
            $id = getIdFromLine($line);
            $name = getNameFromLine($line);
            $this->products[$id] = getProductObjectFromId($id, $name);
        }
    }

    function getProducts()
    {
        return $this->products;
    }

    function getProduct($id)
    {
        return $this->products[$id];
    }
}


// messy logic:
// $lines = getProductFileLines('logs.txt');
// $objects = array();
// foreach ($lines as $line) {
//     $id = getIdFromLine($line);
//     $name = getNameFromLine($line);
//     $objects[$id] = getProductObjectFromId($id, $name);
// }
// var_dump($objects);

$facade = new ProductFacade('logs.txt');
var_dump($facade->getProduct(234));