<?php
/**
 * 原型模式用组合代替继承, 让你获得最大化灵活性的同时, 也避免了平行继承体系
 */

/**
 * 结果:
 *   1. 省掉了一些类; 避免了平行继承层次
 *   2. 额外的灵活性(地球海洋和森林+火星平原)
 *   3. 当生成新产品时, 可以重新设定对象状态(new EarthSea(-1))
 */

/**
 * 注意:
 *   1. 如果产品对象引用了其他对象, 你应该实现 `__clone()` 方法来保证你得到的是深复制
 *      如下代码所示:
 */
class Contained {}

class Container
{
    public $contained;

    function __construct()
    {
        $this->contained = new Contained();
    }

    function __clone()
    {
        // 深复制
        $this->contained = clone $this->contained;
    }
}
