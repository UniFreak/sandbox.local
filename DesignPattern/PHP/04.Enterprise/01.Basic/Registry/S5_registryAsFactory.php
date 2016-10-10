<?php
/**
 * 作为数据对象的工厂的注册表: 注册表不存储一个要提供的对象, 而是先创建一个对象实例,
 * 然后存储对该对象的引用. 它也可以做一些幕后的配置工作
 */
class Registry
{
    private static $instance;
    private $request;

    private function __construct() { }

    static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function treeBuilder()
    {
        if (!isset($this->treeBuilder)) {
            $this->treeBuilder = new TreeBuilder($this->conf()->get('treedir'));
        }
        return $this->treeBuilder;
    }

    function conf()
    {
        if (!isset($this->conf)) {
            $this->conf = new Conf();
        }
        return $this->conf;
    }
}