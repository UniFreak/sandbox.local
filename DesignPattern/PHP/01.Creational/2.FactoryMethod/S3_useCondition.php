<?php
abstract class ApptEncoder
{
    abstract function encode();
}

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

class CommsManager
{
    const BLOGGS = 1;
    const MEGA = 2;
    private $mode = 1;

    function __construct($mode)
    {
        $this->mode = $mode;
    }

    // 使用条件语句
    function getApptEncoder()
    {
        switch ($this->mode) {
            case (self::MEGA):
                return new MegaApptEncoder();
            default:
                return new BloggsApptEncoder();
        }
    }

    // P3: 难以扩展:
    //   这里我们增加一个 getHeaderText, 就需要重复一次条件判断
    //   如果在增加 getFooterText, getMainContent 呢!
    function getHeaderText()
    {
        switch ($this->mode) {
            case (self::MEGA):
                return "MeagCal header\n";
            default:
                return "BloggsCal header\n";
        }
    }
}


$mgr = new CommsManager(CommsManager::MEGA);
$encoder = $mgr->getApptEncoder();
print $encoder->encode();