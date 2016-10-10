<?php
abstract class Lesson
{
    protected $duration;
    private $costStrategy;

    function __construct($duration, CostStrategy $strategy)
    {
        $this->duration = $duration;
        $this->costStrategy = $strategy;
    }

    /**
     * 使用委托, 把计费任务交给策略类
     *
     * 有时候你不知道策略对象需要多少信息来调用其中的方法, 这时你可以将客户端对象实例传
     * 递给策略对象, 将需要获得什么数据的决定权委托给策略对象来完成
     */
    function cost()
    {
        return $this->costStrategy->cost($this);
    }

    /**
     * 同上
     */
    function chargeType()
    {
        return $this->costStrategy->chargeType();
    }

    function getDuration()
    {
        return $this->duration;
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


abstract class CostStrategy
{
    abstract function cost(Lesson $lesson);
    abstract function chargeType();
}

class TimedCostStrategy extends CostStrategy
{
    function cost(Lesson $lesson)
    {
        return ($lesson->getDuration() * 5);
    }

    function chargeType()
    {
        return "hourly rate";
    }
}

class FixedCostStrategy extends CostStrategy
{
    function cost(Lesson $lesson)
    {
        return 30;
    }

    function chargeType()
    {
        return "fixed rate";
    }
}


// ==================== use case ====================
$lecture = new Lecture(5, new FixedCostStrategy());
$seminar = new Seminar(5, new TimedCostStrategy());

print "{$lecture->cost()} ({$lecture->chargeType()})\n";
print "{$seminar->cost()} ({$seminar->chargeType()})\n";