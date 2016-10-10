<?php
/**
 * 此例中, 我们使用组合模式中的例子, 利用组合模式提供的遍历对象树的便利
 *
 * 我们希望能为这些对象增加转储节点文本信息的操作(textDump). 而这里则展示直接把此操作放到
 * 这些对象里
 */

abstract class Unit
{
    function getComposite()
    {
        return null;
    }

    abstract function bombardStrength();

    // 转储节点的文本信息
    function textDump($level = 0)
    {
        $ret = "";
        $pad = 4 * $level;
        $ret .= sprintf("%{$pad}s", "");
        $ret .= get_class($this).": ";
        $ret .= "bombard: ".$this->bombardStrength()."\n";
        return $ret;
    }
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

    // 组合元素的特殊实现
    function textDump($level = 0)
    {
        $ret = parent::textDump($level);
        foreach ($this->units as $unit) {
            $ret .= $unit->textDump($level + 1);
        }
        return $ret;
    }
}


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


// ==================== use case ====================
$mainArmy = new Army();
$mainArmy->addUnit(new Archer());
$mainArmy->addUnit(new LaserCannonUnit());

echo $mainArmy->textDump();

/**
 * P1: 如果还需要其他大量操作(统计树中单元个数, 保存组件到数据库...), 都这样实现的话, 会使
 *     类变得臃肿, 且破坏接口
 */