<?php
/**
 * 需求:
 *   - 需根据条件动态(运行时)生成各自不同类型的对象
 *   - 需要能够相对轻松的加入新的对象类型
 *   - 每一个对象类型都可定制特定的功能
 */

// <abstract product>
abstract class ApptEncoder
{
    abstract function encode();
}

// <concrete product>
class BloggsApptEncoder extends ApptEncoder
{
    function encode()
    {
        return "Appointment data encoded in BloggsCal format\n";
    }
}

// <concrete product>
class MegaApptEncoder extends ApptEncoder
{
    function encode()
    {
        return "Appointment data encoded in MegaCal format\n";
    }
}


// <abstract factory>
abstract class CommsManager
{
    abstract function getHeaderText();
    abstract function getFooterText();
    // <abstract factory method>
    abstract function getApptEncoder();
}

// <concrete factory>
class BloggsCommsManager extends CommsManager
{
    function getHeaderText()
    {
        return "BloggsCal header\n";
    }

    function getFooterText()
    {
        return "BloggsCal footer\n";
    }

    // <concrete factory method>
    function getApptEncoder()
    {
        return new BloggsApptEncoder();
    }

}

// <concrete factory>
class MegaCommsManager extends CommsManager
{
    function getHeaderText()
    {
        return "MegaCal header\n";
    }

    function getFooterText()
    {
        return "MegaCal footer\n";
    }

    // <concrete factory method>
    function getApptEncoder()
    {
        return new MegaApptEncoder();
    }

}


$rand = rand(1, 2);
switch ($rand) {
    case 1:
        $mgr = new BloggsCommsManager();
        break;
    case 2:
        $mgr = new MegaCommsManager();
}

$encoder = $mgr->getApptEncoder();
print $encoder->encode();

/**
 * 结果:
 *   1. 注意创建者类与产品类的层次结构是非常相似的, 这构成了特殊的代码重复(平行继承层次)
 *      --> Prototype
 *   2. 可能会导致不必要的子类化. 如果你为创建者类创建子类的原因只是为了实现工厂方法模式
 *      那么最好再考虑一下
 *   3. 不能处理更复杂的层次结构(如要增加处理"待办事宜"及"联系人" -- 纵向的增长)
 *      --> AbstractFactory
 */