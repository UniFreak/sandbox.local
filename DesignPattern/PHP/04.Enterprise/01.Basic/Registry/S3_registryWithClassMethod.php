<?php
/**
 * 使用类方法(getRequest, setRequest)实现数据类的读写
 * 因为 set* 方法的类型提示, 这样能保证 get* 方法返回的是预期中的对象类型
 *
 * 同时使用了单例模式
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

    function getRequest()
    {
        return $this->request;
    }

    function setRequest(Request $request)
    {
        $this->request = $request;
    }
}

class Request {}

// ==================== use case ====================
// 在系统的某个地方添加 Request 对象
$reg = Registry::instance();
$reg->setRequest(new Request());

// 在系统的两外一个地方读取它
$reg = Registry::instance();
print_r($reg->getRequest());