<?php

// ==================== acceptors(组合模式) ====================
abstract class Unit
{
    protected $depth = 0;

    function getComposite()
    {
        return null;
    }

    abstract function bombardStrength();


    /**
     * 接纳访问者并执行其操作(针对局部对象)
     */
    function accept(ArmyVisitor $visitor)
    {
        $method = "visit".get_class($this);
        $visitor->$method($this);
    }

    /**
     * 设置层次深度(在 addUnit 时被调用)
     */
    protected function setDepth($depth)
    {
        $this->depth = $depth;
    }

    function getDepth()
    {
        return $this->depth;
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
        // 添加单元并递增层次深度
        $unit->setDepth($this->depth+1);
        $this->units[] = $unit;
    }

    function removeUnit(Unit $unit)
    {
        $this->units = array_udiff(
            $this->units,
            array($unit),
            function($a, $b) {return ($a === $b) ? 0 : 1;});
    }


    /**
     * 接纳访问者并执行其操作(针对组合对象)
     */
    function accept(ArmyVisitor $visitor)
    {
        parent::accept($visitor);
        foreach ($this->units as $unit) {
            $unit->accept($visitor);
        }
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


// ==================== visitors ====================
abstract class ArmyVisitor
{
    abstract function visit(Unit $node);

    function visitArcher(Archer $node)
    {
        $this->visit($node);
    }

    function visitLaserCannonUnit(LaserCannonUnit $node)
    {
        $this->visit($node);
    }

    function visitTroopCarrier(TroopCarrier $node)
    {
        $this->visit($node);
    }

    function visitArmy(Army $node)
    {
        $this->visit($node);
    }
}

/**
 * 文本转储操作访问者
 */
class TextDumpArmyVisitor extends ArmyVisitor
{
    private $text = "";

    function visit(Unit $node)
    {
        $ret = "";
        $pad = 4 * $node->getDepth();
        $ret .= sprintf("%${pad}s", "");
        $ret .= get_class($node).": ";
        $ret .= "bombard: ".$node->bombardStrength()."\n";
        $this->text .= $ret;
    }

    function getText()
    {
        return $this->text;
    }
}

/**
 * 征税操作访问者
 *
 * 针对不同的单元类型征收不同的费用
 */
class TaxCollectionVisitor extends ArmyVisitor
{
    private $due = 0;
    private $report = "";

    function visit(Unit $node)
    {
        $this->levy($node, 1);
    }

    function visitArcher(Archer $node)
    {
        $this->levy($node, 2);
    }

    function visitTroopCarrier(TroopCarrier $node)
    {
        $this->levy($node, 5);
    }

    private function levy(Unit $unit, $amount)
    {
        $this->report .= "Tax levied for ".get_class($unit);
        $this->report .= ": $amount\n";
        $this->due += $amount;
    }

    function getReport()
    {
        return $this->report;
    }

    function getTax()
    {
        return $this->due;
    }
}


// ==================== use case ====================
$mainArmy = new Army();
$mainArmy->addUnit(new Archer());
$mainArmy->addUnit(new LaserCannonUnit());
$mainArmy->addUnit(new TroopCarrier());

$textDump = new TextDumpArmyVisitor();
$mainArmy->accept($textDump);
print $textDump->getText();

$taxCollecotr = new TaxCollectionVisitor();
$mainArmy->accept($taxCollecotr);
print "TOTAL: \n";
print $taxCollecotr->getTax()."\n";
print "REPORT: \n";
print $taxCollecotr->getReport()."\n";