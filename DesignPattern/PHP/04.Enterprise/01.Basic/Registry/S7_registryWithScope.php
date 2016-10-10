<?php
/**
 * 本章视图基础且完整的系统, 系统的名字为 woo(What's On Outside), 所以这里开始使用命名空间
 */
namespace woo\base;

abstract class Registry
{
    abstract protected function get($key);
    abstract protected function set($key, $val);
}

// ==================== 请求级别 ====================
class RequestRegistry extends Registry
{
    private $value = array();
    private static $instance;

    private function __construct() {}

    static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function get($key)
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }

    protected function set($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * 这里虽然内部使用的是键值对存储数据对象, 但是向外部提供类方法的访问接口
     */
    static function getRequest()
    {
        return self::instance()->get('reqeust');
    }

    static function setRequest(\woo\controller\Request $request)
    {
        return self::instance()->set('request', $request);
    }
}

// ==================== 会话级别 ====================
class SessionRegistry extends Registry
{
    private static $instance;

    private function __construct()
    {
        session_start(); // 利用 PHP 内建的 session 机制
    }

    static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function get($key)
    {
        if (isset($_SESSION[__CLASS__][$key])) {
            return $_SESSION[__CLASS__][$key];
        }
        return null;
    }

    protected function set($key, $val)
    {
        $_SESSION[__CLASS__][$key] = $val;
    }

    function setComplex(Complex $complex)
    {
        self::instance()->set('complex', $complex);
    }

    function getComplex()
    {
        return self::instance()->get('complex');
    }
}

// ==================== 应用级别 ====================
/**
 * 这里使用的是文件系统, 并且每个变量都要打开一次文件, 这样的效率并不高
 * 可以考虑适用 memcached 或 redis, 或者把全部变量存到一个文件中
 *
 * 如果混乱的保存数据, 两个进程将会产生冲突, 你可以
 * - 实现一个锁定方案来防止冲突
 * - 把 ApplicationRegistry 当做一个大型的只读资源
 */
class ApplicationRegistry extends Registry
{
    private static $instance;
    private $freezeDir = "data";
    private $values = array();
    private $mtimes = array(); // 存储最后修改时间, 用于检测更新

    private function __construct() {}

    static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function get($key)
    {
        $path = $this->freezeDir . DIRECTORY_SEPARATOR . $key;
        if (file_exists($path)) {
            clearstatcache();
            $mtime = filemtime($path);
            if (!isset($this->mtimes[$key])) {
                $this->mtimes[$key] = 0;
            }
            if ($mtime > $this->mtimes[$key]) {
                $data = file_get_contents($path);
                $this->mtimes[$key] = $mtime;
                return ($this->values[$key] = unserialize($data));
            }
        }
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }
        return null;
    }

    protected function set($key, $val)
    {
        $this->value[$key] = $val;
        $path = $this->freezeDir . DIRECTORY_SEPARATOR . $key;
        file_put_contents($path, serialize($val));
        $this->mtimes[$key] = time();
    }

    static function getDSN()
    {
        return self::instance()->get('dsn');
    }

    static function setDSN($dsn)
    {
        return self::instance()->set('dsn', $dsn);
    }
}