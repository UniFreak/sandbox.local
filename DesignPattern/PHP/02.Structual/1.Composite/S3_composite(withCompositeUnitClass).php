<?php

// <base unit>
abstract class Unit
{
    /**
     * 用于利于客户端检测对象是否是一个组合对象, 以决定调用 add/remove 是否安全
     */
    function getComposite()
    {
        return null;
    }

    // <common(leaf&composite) operations>
    abstract function bombardStrength();
}

abstract class CompositeUnit extends Unit
{
    protected $units = array();

    function getComposite()
    {
        return $this;
    }

    protected function units()
    {
        return $this->units;
    }

    /**
     * 组合对象特定操作放到此类中
     */
    // <composite units operations>
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
}


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
class Army extends CompositeUnit
{
    function bombardStrength()
    {
        $ret = 0;
        foreach ($this->units as $unit) {
            $ret += $unit->bombardStrength();
        }
        return $ret;
    }
}

class TroopCarrier extends CompositeUnit
{
    function addUnit(Unit $unit)
    {
        if ($unit instanceof Cavalry) {
            throw new UnitExcpetion("can't get a horse on the vehicle");
        }
        parent::addUnit($unit);
    }

    function bombardStrength()
    {
        return 0;
    }
}

// <use case>
class UnitScript
{
    static function joinExisting(Unit $newUnit, Unit $occupyingUnit)
    {
        $comp;

        /**
         * 客户端使用 getComposite 方法的返回值判断对象是否是组合对象
         */
        if (!is_null($comp = $occupyingUnit->getComposite())) {
            $comp->addUnit($newUnit);
        } else {
            $comp = new Army();
            $comp->addUnit($occupyingUnit);
            $comp->addUnit($newUnit);
        }

        return $comp;
    }
}

$occupying = new Archer(); // 4

$newComer = new Army();
$newComer->addUnit(new Archer()); // 4
$newComer->addUnit(new LaserCannonUnit()); // 44

$comp = UnitScript::joinExisting($newComer, $occupying);

$newComer2 = new TroopCarrier();
$newComer2->addUnit(new Archer()); // 4

$comp2 = UnitScript::joinExisting($comp, $newComer2);
print "attacking with strength: {$comp->bombardStrength()}\n";


/**
 *   pros:
 *   - 可以确保调用 Unit::addUnit() 是否安全(通过 getComposite 方法)
 */