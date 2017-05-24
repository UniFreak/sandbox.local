<?php
/**
 * 信审系统
 */
class CreditSystem
{
    private $creditAssigner;

    public function __construct($request)
    {
        if ($request->forNewCar) {
            $this->creditAssigner = new NewCarCreditAssigner();
        } elseif ($request->forUsedCar) {
            $this->creditAssigner = new UsedCarCreditAssigner();
        }
    }

    public function assignTaskFor($request)
    {
        // 委托
        return $this->creditAssigner->assignTaskFor($request);
    }
}

/**
 * 信审请求
 */
class Request
{
    public $forNewCar;
    public $forUsedCar;
    public $fromUlQr;

    public function __construct($forNewCar, $forUsedCar, $fromUlQr)
    {
        $this->forNewCar = $forNewCar;
        $this->forUsedCar = $forUsedCar;
        $this->fromUlQr = $fromUlQr;
    }
}

/**
 * 策略接口
 */
abstract class CreditAssigner
{
    abstract public function assignTaskFor($request);

    protected function assignTo($capital)
    {
        echo 'assigned to ' . $capital;
    }
}

/**
 * 二手车策略
 */
class UsedCarCreditAssigner extends CreditAssigner
{
    public function assignTaskFor($request)
    {
        if ($request->fromUlQr) {
            return $this->assignTo('ul');
        }
        return $this->assignTo('wb');
    }
}

/**
 * 新车策略
 */
class NewCarCreditAssigner extends CreditAssigner
{
    public function assignTaskFor($request)
    {
        return $this->assignTo('cz');
    }
}

/**
 * ====================demo====================
 */
$request = new Request($forNewCar = false, $forUsedCar = true, $fromUlQr = true);
$sys = new CreditSystem($request);
$sys->assignTaskFor($request);