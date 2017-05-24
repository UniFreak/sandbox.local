<?php
// ==================== 使用委托实现 ISP ====================
class Timer
{
    public function register($timeout, TimerClient $client)
    {
        $this->timeout = $timeout;
        $this->client = $client;
    }

    public function run()
    {
        $secondPassed = 0;
        while ($secondPassed < $this->timeout) {
            sleep(1);
            $secondPassed++;
        }
        $this->client->timeout();
    }
}

abstract class TimerClient
{
    abstract function timeout();
}

class Door
{
    public function lock() {}
    public function unlock() {}

    public function isOpen()
    {
        return true;
    }
}

class DoorTimerAdapter extends TimerClient
{
    private $timedDoor;

    public function __construct($timedDoor)
    {
        $this->timedDoor = $timedDoor;
    }

    public function timeout()
    {
        // 使用委托, 将 TimerClient(timetout) 适配为 TimedDoor(doorTimeout)
        $this->timedDoor->doorTimeout();
    }
}

class TimedDoor extends Door
{
    /**
     * 这里特意把接口名称改为 doorTimeout, 是为了突出
     * 适配器 "将 TimerClient 接口转换成 TimedDoor 接口"
     */
    public function doorTimeout()
    {
        if ($this->isOpen()) {
            echo 'warning warning...';
        }
    }
}

// ==================== demo ====================
$adaptor = new DoorTimerAdapter(new TimedDoor());
$timer = new Timer();
$timer->register(5, $adaptor);
$timer->run();


/**
 * 结果:
 * - 适配器在实现了 TimedDoor 的同时, 解耦了 TimerClient 和 Door
 *
 * 优先选择使用多重继承(@see TimedDoor3.php), 只有
 * - 适配器所做的转换是必须的, 或者
 * - 不同的时候需要不同的转换时
 * 才会选择这种方式
 */