<?php
/**
 * Problem:
 * 工厂方法解决了 '横向' 的增长, 那 '纵向' 的增长怎么办?
 */

/**
 * Implementation:
 * 把 '纵向'(1, 2) 的增长放到创建者类中
 */
abstract class AbstractProduct1 {}

class ConcreteProduct1A extends AbstractProduct1 {}

class ConcreteProduct1B extends AbstractProduct1 {}

abstract class AbstractProduct2 {}

class ConcreteProdcut2A extends AbstractProduct2 {}

class ConcreteProdcut2B extends AbstractProduct2 {}


abstract class AbstractCreator {
    abstract function createProduct1();

    abstract function createProduct2();
}

class ConcreteCreatorA extends AbstractCreator {
    function createProduct1() {
        return new ConcreteProduct1A();
    }

    function createProduct2() {
        return new ConcreteProduct2A();
    }
}

class ConcreteCreatorB extends AbstractCreator {
    function createProduct1() {
        return new ConcreteProdcut1B();
    }

    function createProduct2() {
        return new ConcreteProdcut2B();
    }
}

// 也可把创建动作放到条件语句中, 像这样:
// abstract class AbstractCreator {
//     abstract function make($type);
// }

// class ConcreteCreatorA extends AbstractCreator {
//     function make($type) {
//         if ($type == '1') {
//             return new ConcreteProduct1A();
//         } elseif ($type == '2') {
//             return new ConcreteProduct2A();
//         }
//     }
// }

// class ConcreteCreatorB extends AbstractCreator {
//     function make($type) {
//         if ($type == '1') {
//             return new concreteProduct1B();
//         } elseif ($type == '2') {
//             return new concreteProduct2B();
//         }
//     }
// }

/**
 * Result:
 * 1. 并没有解决在工厂模式方法中的平行继承层次
 * 2. 没添加一个新产品, 就被迫去维护对应的创建者实现
 * 3. 并不灵活
 */