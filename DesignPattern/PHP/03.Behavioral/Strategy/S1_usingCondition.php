<?php
abstract class Lesson
{
    protected $duration;
    const FIXED = 1;
    const TIMED = 2;
    private $costType;

    function __construct($duration, $costType = 1)
    {
        $this->duration = $duration;
        $this->costType = $costType;
    }

    function cost()
    {
        switch ($this->costType) {
            case self::TIMED:
                return (5 * $this->duration);
                break;
            case self::FIXED:
                return 30;
                break;
            default:
                $this->costType = self::FIXED;
                return 30;
        }
    }

    function chargeType()
    {
        switch ($this->costType) {
            case self::TIMED:
                return "hourly rate";
                break;
            case self::FIXED:
                return "fixed rate";
                break;
            default:
                $this->costType = self::FIXED;
                return "fixed rate";
        }
    }
}


class Lecture extends Lesson
{
    // lecture things...
}

class Seminar extends Lesson
{
    // seminar things...
}


// ==================== use case ====================
$lecture = new Lecture(5, Lesson::FIXED);
$seminar = new Seminar(5, Lesson::TIMED);

print "{$lecture->cost()} ({$lecture->chargeType()})\n";
print "{$seminar->cost()} ({$seminar->chargeType()})\n";

/**
 * P2: 用条件语句的重复代替继承的重复是一种倒退(通常使用条件语句是为了实现多态)
 */