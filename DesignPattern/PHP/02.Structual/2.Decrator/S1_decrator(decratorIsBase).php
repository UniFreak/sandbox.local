<?php

// <abstract base>
abstract class Tile
{
    abstract function getWealthFactor();
}

// <component>
class Plains extends Tile
{
    private $wealthFactor = 2;

    function getWealthFactor()
    {
        return $this->wealthFactor;
    }
}

/**
 * 扩展自基类
 */
// <decorator base>
abstract class TileDecorator extends Tile
{
    /**
     * 拥有指向基类对象的引用(组合)
     */
    protected $tile;

    function __construct(Tile $tile)
    {
        $this->tile = $tile;
    }
}

// <decorators>
class DiamondDecrator extends TileDecorator
{
    function getWealthFactor()
    {
        /**
         * 当 getWealthFactor 被调用时:
         * - 先调用做引用的 Tile 对象的 getWealthFactor(委托)
         * - 然后执行自己特有的操作(装饰: + 2)
         */
        return $this->tile->getWealthFactor() + 2;
    }
}

class PollutionDecrator extends TileDecorator
{
    function getWealthFactor()
    {
        return $this->tile->getWealthFactor() - 4;
    }
}

/**
 * 通过犹如"管道"串联起来一般的调用方式, 灵活合并对象
 */
$tile = new Plains();
print $tile->getWealthFactor() . "\n";

$tile = new DiamondDecrator(new Plains());
print $tile->getWealthFactor() . "\n";

$tile = new PollutionDecrator(new DiamondDecrator(new Plains()));
print $tile->getWealthFactor() . "\n";