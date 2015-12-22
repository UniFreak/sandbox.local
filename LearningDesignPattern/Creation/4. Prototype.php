<?php
/**
 * Problem:
 * 1. 当继承体系越来越宽, 维度越来越多(不仅限于横向, 纵向, 甚至 Z 向?) 时, 工厂模式
 *    变得庞大而不灵活
 */

/**
 * Implementation:
 * 1. 动态传入 '横向(A,B)' 类型实例以决定工厂生产的产品 '横向' 类型
 * 2. 动态调用 '纵向(1,2)' 类型实例以决定工厂生产的产品 '纵向' 类型
 */
class ConcreteProduct1 {}
class ConcreteProduct1A extends ConcreteProduct1 {}
class ConcreteProduct1B extends ConcreteProdcut1 {}

class ConcreteProduct2 {}
class ConcreteProduct2A extends ConcreteProduct2 {}
class ConcreteProduct2B extends ConcreteProduct2 {}

class DynamicMixFactory {
    private $product1;
    private $product2;

    public function __construct(
        ConcreteProduct1 $product1,
        Concreteproduct2 $product2
    ) {
        $this->product1 = $product1;
        $this->product2 = $product2;
    }

    public function getProduct1() {
        return clone $this->product1;
    }

    public function getProduct2() {
        return clone $this->product2;
    }
}

$factory = new DynamicMixFactory(new ConcreteProduct1B, new ConcreteProdcut2A);
$product1 = $factory->getProduct1();
$product2 = $factory->getProduct2();

/**
 * Result:
 * 1. 省掉了一些类
 * 2. 获得了额外的灵活性
 */