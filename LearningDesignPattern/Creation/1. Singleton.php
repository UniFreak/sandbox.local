<?php
/**
 * You require:
 * 1. 该对象可以被系统中的任何对象使用
 * 2. 该对象不能存储在全局变量中, 因为那样存在被覆写的风险
 * 3. 系统中不应超过一个该对象
 */

/**
 * Impelementation:
 * 1. 一个私有的 $instance, 用于保存自身对象
 * 2. 一个私有的构造方法, 防止从外部实例化自身
 * 3. 一个公有的工厂方法(getInstance):
 *     1. 判断 $instance 是否为空
 *     2. 是, 则实例化自身并赋值给 $instance
 *     3. 否, 则直接返回 $instance
 *     从而保证整个系统只使用同一个 Singleton 对象
 */
class Singleton {
    private static $instance;

    private function __construct() {}

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

/**
 * Result:
 * 1. 如果改变一个单例, 那么所有使用该单例的类都可能会受到影响
 * 2. 是对全局变量的改进, 但是要小心部署
 */