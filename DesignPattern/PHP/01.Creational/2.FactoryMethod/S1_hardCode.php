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
    function getApptEncoder()
    {
        // 硬编码
        return new BloggsApptEncoder();
    }
}

$mgr = new CommsManager();
$encoder = $mgr->getApptEncoder();
print $encoder->encode();

// P1: 完全没有灵活性