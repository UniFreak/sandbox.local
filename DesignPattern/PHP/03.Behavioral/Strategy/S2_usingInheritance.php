<?php
/**
 * 该例中, 设我们需要为大学课程建模. 这些课程可分为演讲课研讨会, 各个课程可能又各自的计
 * 费方式, 如定价计费或按时计费
 */

abstract class Lesson
{
    protected $duration;

    abstract function cost();
    abstract function chargeType();

    function __construct($duration)
    {
        $this->duration = $duration;
    }
}


abstract class Lecture extends Lesson
{
    // lecture things...
}

class FixedPriceLecture extends Lecture
{
    function cost()
    {
        return 30;
    }

    function chargeType()
    {
        return "fixed rate";
    }
}

class TimedPriceLecture extends Lecture
{
    function cost()
    {
        return (5 * $this->duration);
    }

    function chargeType()
    {
        return "hourly rate";
    }
}


abstract class Seminar extends Lesson
{
    // seminar things...
}

class FixedPriceSeminar extends Seminar
{
    function cost()
    {
        return 30;
    }

    function chargeType()
    {
        return "fixed rate";
    }
}

class TimedPriceSeminar extends Seminar
{
    function cost()
    {
        return (5 * $this->duration);
    }

    function chargeType()
    {
        return "hourly rate";
    }
}


// ==================== use case ====================
$lecture = new FixedPriceLecture(5);
$seminar = new TimedPriceSeminar(5);

print "{$lecture->cost()} ({$lecture->chargeType()})\n";
print "{$seminar->cost()} ({$seminar->chargeType()})\n";

/**
 * P1: 大量重复实现(cost, chargeType)
 */