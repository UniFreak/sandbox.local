<?php

// <base unit>
abstract class Unit
{
    /**
     * 把 add/remove 操作放到基类, 强制所有子类实现, 即使是叶子单元
     */
    // <composite operations>
    abstract function addUnit(Unit $unit);
    abstract function removeUnit(Unit $unit);

    // <common(leaf&composite) operations>
    abstract function bombardStrength();
}

class UnitException extends Exception {}

// <leaf units>
class Archer extends Unit
{
    /**
     * 使用异常确保叶子单元不能被添加/移除其他单元
     */
    function addUnit(Unit $unit)
    {
        throw new Exception(get_class($this) . " is a leaf");
    }

    function removeUnit(Unit $unit)
    {
        throw new Exception(get_class($this) . " is a leaf");
    }

    function bombardStrength()
    {
        return 4;
    }
}

class LaserCannonUnit extends Unit
{
    function addUnit(Unit $unit)
    {
        throw new Exception(get_class($this) . " is a leaf");
    }

    function removeUnit(Unit $unit)
    {
        throw new Exception(get_class($this) . " is a leaf");
    }

    function bombardStrength()
    {
        return 44;
    }
}

// <composite unit>
class Army extends Unit
{
    private $units = array();

    function addUnit(Unit $unit)
    {
        if (in_array($unit, $this->units, true)) {
            return;
        }
        $this->units[] = $unit;
    }

    function removeUnit(Unit $unit)
    {
        $this->units = array_udiff(
            $this->units,
            array($unit),
            function($a, $b) {return ($a === $b) ? 0 : 1;});
    }

    function bombardStrength()
    {
        $ret = 0;
        foreach ($this->units as $unit) {
            $ret += $unit->bombardStrength();
        }
        return $ret;
    }
}


// <use case>
$mainArmy = new Army();
$mainArmy->addUnit(new Archer()); // 4
$mainArmy->addUnit(new LaserCannonUnit()); // 44

$subArmy = new Army();
$subArmy->addUnit(new Archer()); // 4
$subArmy->addUnit(new Archer()); // 4
$subArmy->addUnit(new Archer()); // 4

$mainArmy->addUnit($subArmy);

print "attacking with strength: {$mainArmy->bombardStrength()}\n";

/**
 *   pros:
 *   - 确保所有类都共享同一个接口
 *   cons:
 *   - 叶子单元无必要的 add/remove
 *   - 要求修改所有叶子类的 add/remove 方法 --> S2
 *   - 仍无法确保调用 Unit::addUnit() 是否安全(可能会抛异常)
 */