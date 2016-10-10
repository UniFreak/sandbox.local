<?php
/**
 * 使用键值对实现数据类的读写
 *
 * pros:
 * - 不需要为希望存储和访问的每个对象都创建类方法
 * cons:
 * - 重新引入了全局变量: 添加一个对象到系统时可以随意覆盖已有的键值对
 */
class Registry
{
    private static $instance;
    private $values = array();

    private function __construct() { }

    static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function get($key)
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }

    function set($key, $value)
    {
        $this->values[$key] = $value;
    }
}

class Request {}

// ==================== use case ====================
// 在系统的某个地方添加 Request 对象
$reg = Registry::instance();
$reg->set('request', new Request());

// 在系统的两外一个地方读取它
$reg = Registry::instance();
print_r($reg->get('request'));