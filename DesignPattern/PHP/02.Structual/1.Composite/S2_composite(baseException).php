<?php

// <base unit>
abstract class Unit
{
    /**
     * 把 add/remove 操作放到基类, 并默认实现异常
     */
    // <composite operations>
    function addUnit(Unit $unit)
    {
        throw new Exception(get_class($this) . " is a leaf");
    }

    function removeUnit(Unit $unit)
    {
        throw new Exception(get_class($this) . " is a leaf");
    }


    // <common(leaf&composite) operations>
    abstract function bombardStrength();
}

class UnitException extends Exception {}

// <leaf units>
class Archer extends Unit
{
    function bombardStrength()
    {
        return 4;
    }
}

class LaserCannonUnit extends Unit
{
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
 *   - 去除叶子类中的重复代码
 *   cons:
 *   - 组合类中不再需要强制性的实现 add/remove
 *   - 仍无法确保调用 Unit::addUnit() 是否安全(可能会抛异常)
 */