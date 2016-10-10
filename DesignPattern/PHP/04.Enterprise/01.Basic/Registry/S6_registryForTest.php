<?php
/**
 * 注册表对象对于测试也很有用
 */

class Registry
{
    private static $instance;
    private static $testMode;
    private $request;

    private function __construct() { }

    static function instance()
    {
        if (self::$testMode) { // 测试模式
            return new MockRegistry();
        }

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    static function testMode($mode = true)
    {
        self::$instance = null;
        self::$testMode = $mode;
    }
}

// ==================== use case ====================
Registry::testMode();
$mockReg = Registry::instance();