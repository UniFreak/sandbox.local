<?php
/**
 * 纵向的增长由工厂内的工厂方法的纵向增长(getApptEncoder; getTtdEncoder; getContractEncoder)对应并解决
 * 横向的增长由工厂类的横向增长(BloggsCommsManager; MegaCommsManager)对应并解决
 */

// ==================== Factories ====================
// <abstract factory>
abstract class CommsManager
{
    abstract function getHeaderText();
    abstract function getFooterText();

    // <abstract factory methods>
    abstract function getApptEncoder();
    abstract function getTtdEncoder();
    abstract function getContactEncoder();
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

    // <concrete factory methods>
    function getApptEncoder()
    {
        return new BloggsApptEncoder();
    }

    function getTtdEncoder()
    {
        return new BloggsTtdEncoder();
    }

    function getContactEncoder()
    {
        return new BloggsContactEncoder();
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

    // <concrete factory methods>
    function getApptEncoder()
    {
        return new MegaApptEncoder();
    }

    function getTtdEncoder()
    {
        return new MegaTtdEncoder();
    }

    function getContactEncoder()
    {
        return new MegaContactEncoder();
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