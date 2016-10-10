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
    private $encoder;

    // 外部传递
    public function __construct(ApptEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    function getApptEncoder()
    {
        return $this->encoder;
    }
}

$mgr = new CommsManager(new MegaApptEncoder());
$encoder = $mgr->getApptEncoder();
print $encoder->encode();

// P2: 只是延缓了创建问题而已