<?php
class Sea
{
    private $navigability = 0;

    function __construct($navigability)
    {
        $this->navigability = $navigability;
    }
}
class EarthSea extends Sea {}
class MarsSea extends Sea {}

class Plains {}
class EarthPlains extends Plains {}
class MarsPlains extends Plains {}

class Forest {}
class EarthForest extends Forest {}
class MarsForest extends Forest {}

class TerrainFactory
{
    private $sea;
    private $forest;
    private $plains;

    /**
     * 在初始化时, 就保存具体产品的实例(具体实例化哪些产品, 可能由配置文件决定)
     */
    function __construct(Sea $sea, Plains $plains, Forest $forest)
    {
        $this->sea = $sea;
        $this->plains = $plains;
        $this->forest = $forest;
    }

    /**
     * 工厂方法, 只是简单的返回实例的副本
     */
    function getSea()
    {
        return clone $this->sea;
    }

    function getPlains()
    {
        return clone $this->plains;
    }

    function getForest()
    {
        return clone $this->forest;
    }
}

/**
 * 动态传入不同的"横向"类型的产品实例, 以决定"横向"的类型
 */
$factory = new TerrainFactory(new EarthSea(-1), new MarsPlains(), new EarthForest());
/**
 * 动态调用"纵向"类型的工厂方法, 以决定"纵向"的类型
 */
print_r($factory->getSea());
print_r($factory->getPlains());
print_r($factory->getForest());