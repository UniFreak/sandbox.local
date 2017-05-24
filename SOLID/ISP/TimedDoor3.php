<?php
// ==================== 使用多重继承(假)实现 ISP ====================
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

interface TimerClient
{
    public function timeout();
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

class TimedDoor extends Door implements TimerClient
{
    public function timeout()
    {
        if ($this->isOpen()) {
            echo 'warning warning...';
        }
    }
}

// ==================== demo ====================
$timedDoor = new TimedDoor();
$timer = new Timer();
$timer->register(5, $timedDoor);
$timer->run();