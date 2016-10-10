<?php
/**
 * 纵向的增长由工厂内的 make 方法中条件判断的纵向增长(switch...case)对应并解决
 * 横向的增长由工厂类的横向增长(BloggsCommsManager; MegaCommsManager)对应并解决
 */

// ==================== Factories ====================
// <abstract factory>
abstract class CommsManager
{
    // flags
    const APPT = 1;
    const TTD = 2;
    const CONTACT = 3;

    abstract function getHeaderText();
    abstract function getFooterText();

    // <abstract creator methods>
    abstract function make($flag_int);
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

    // <concrete creator methods>
    function make($flag_int)
    {
        switch ($flag_int) {
            case self::APPT:
                return new BloggsApptEncoder();
            case self::CONTACT:
                return new BloggsContactEncoder();
            case self::TTD:
                return new BloggsTtdEncoder();
        }
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

    // <concrete creator methods>
    function make($flag_int)
    {
        switch ($flag_int) {
            case self::APPT:
                return new MegaApptEncoder();
            case self::CONTACT:
                return new MegaContactEncoder();
            case self::TTD:
                return new MegaTtdEncoder();
        }
    }

}


// ==================== Products ====================
// <abstract product>
abstract class ApptEncoder
{
    abstract function encode();
}

// <concrete products>
class BloggsApptEncoder extends ApptEncoder
{
    function encode()
    {
        return "Appointment data encoded in BloggsCal format\n";
    }
}

class MegaApptEncoder extends ApptEncoder
{
    function encode()
    {
        return "Appointment data encoded in MegaCal format\n";
    }
}

// <abstract product>
abstract class TtdEncoder
{
    abstract function encode();
}

// <concrete products>
class BloggsTtdEncoder extends TtdEncoder
{
    function encode()
    {
        return "Todo data encoded in BloggsCal format\n";
    }
}

class MegaTtdEncoder extends TtdEncoder
{
    function encode()
    {
        return "Todo data encoded in MegaCal format\n";
    }
}

// <abstract product>
abstract class ContactEncoder
{
    abstract function encode();
}

// <concrete products>
class BloggsContactEncoder extends ContactEncoder
{
    function encode()
    {
        return "Contact data encoded in BloggsCal format\n";
    }
}

class MegaContactEncoder extends ContactEncoder
{
    function encode()
    {
        return "Contact data encoded in MegaCal format\n";
    }
}

/**
 * 结果:
 *   pros:
 *   1. 类的接口变得更紧凑
 *   cons:
 *   2. 引入平行条件, 因为每个具体创建者都必须实现相同的标志检测
 *   3. 破坏了接口的清晰度, 客户类无法确定具体的创建者是否可以生成所有产品
 */