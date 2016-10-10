<?php
// 使用单例, 一般是为了实现全局访问的便利性, 又避免全局变量和传递对象这两个方法的缺陷

/**
 * 需求:
 *   1. 该对象可以被系统中的任何对象使用(全局访问的便利性)
 *   2. 该对象不能存储在全局变量中, 因为
 *       - 全局变量将类绑定于特定环境(该环境必须有此类用到的全局变量)
 *       - 全局变量易导致命名冲突, 更糟糕的是 PHP 不会对此冲突给出任何警告
 *   3. 不希望使用"在对象中来回传递此对象"这个方法, 因为
 *       - 这产生另一种形式的耦合
 *       - 这并不能保证所有其他对象都使用同一个此对象
 *      即是要求, 系统中不应超过一个该对象
 */

/**
 * 实现: 单例模式
 */
class Preferences
{
    private $props = array();

    // 私有的, 静态的 $instance, 用于保存自身对象
    private static $instance;

    // 私有的构造方法, 防止从外部实例化自身
    private function __construct() {}

    // 公有的工厂方法
    private static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new Preferences();
        }
        return self::$instance;
    }
}

/**
 * 结果:
 *   pros:
 *     - 对全局变量的改进
 *     - 避免了不必要的对象传递
 *   cons:
 *     - 如果改变一个单例, 那么所有使用该单例的类都可能会受到影响
 *     - 导致很难调试的依赖关系, 要小心部署
 */