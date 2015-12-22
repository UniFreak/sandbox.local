<?php
/**
 * Problem:
 * 1. 在代码运行时我们才知道要生成的对象类型
 * 2. 需要能够相对轻松的加入新的对象类型
 * 3. 每一个对象类型都可定制特定的功能
 */

/**
 * Implementation:
 * 1. 将创建者和产品类分开
 * 2. 一般来说创建者类的每个子类实例化一个相应产品子类
 */
abstract class AbstractProduct {}

class ConcreteProductA extends AbstractProduct {}

class ConcreteProductB extends AbstractProduct {}


abstract class AbstractCreator {
    abstract function createProduct();
}

class ConcreteCreatorA extends AbstractCreator {
    function createProduct() {
        return new ConcreteProductA();
    }
}

class ConcreteCreatorB extends AbstractCreator {
    function createProduct() {
        return new ConcreteProductB();
    }
}

/**
 * Result:
 * 1. 注意创建者类与产品类的层次结构是非常相似的, 这构成了特殊的代码重复(平行继承层次)
 * 2. 可能会导致不必要的子类化. 如果你为创建者类创建子类的原因只是为了实现工厂方法模式
 *    那么最好再考虑一下
 */