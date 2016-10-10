<?php
/**
 * 下面是一些故意让人混淆的不着边际的代码, 其功能只是从文件中获取 log 并将它转换为对象数据
 */

function getProductFileLines($file)
{
    return file($file);
}

function getProductObjectFromId($id, $productName)
{
    return new Product($id, $productName);
}

function getNameFromLine($line)
{
    if (preg_match("/.*-(.*)\s\d+/", $line, $array)) {
        return str_replace('_', ' ', $array[1]);
    }
    return '';
}

function getIdFromLine($line)
{
    if (preg_match("/^(\d{1,3})-/", $line, $array)) {
        return $array[1];
    }
    return -1;
}

class Product
{
    public $id;
    public $name;

    function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
