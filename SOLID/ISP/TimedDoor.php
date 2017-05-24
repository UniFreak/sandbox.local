<?php
/**
 * 场景: 使用 Timer 和 TimerClient 机制, 实现定时报警门
 */

// ==================== bad design ====================
class Timer
{
    private $registerd = [];

    public function register($timeout, TimerClient $client)
    {
        array_push($this->registerd, [
            'timeout' => $timeout,
            'client' => $client,
        ]);
    }

    public function run()
    {
        $secondPassed = 0;
        while (1) {
            sleep(1);
            $secondPassed++;
            foreach ($this->registerd as $registerd) {
                if ($registerd['timeout'] == $secondPassed) {
                    $registerd['client']->timeout();
                }
            }
        }
    }
}

abstract class TimerClient
{
    abstract function timeout();
}

class Door extends TimerClient
{
    public function lock() {}
    public function unlock() {}

    public function isOpen()
    {
        return true;
    }

    public function timeout()
    {
        if ($this->isOpen()) {
            echo 'warning warning...';
        }
    }
}

class TimedDoor extends Door {}

// ==================== demo ====================
$timedDoor = new TimedDoor();
$timer = new Timer();
$timer->register(5, $timedDoor);
$timer->run();


/**
 * 后果:
 * - Door 中的 timeout 只是为了 TimedDoor 一个子类, 如果要增加别的子类, 则需要
 *   提供退化实现, 导致违反 LSP
 * - 不仅需要提供退化实现, 如果新增子类需要一个新方法, 按照这种方式, 会使接口变胖
 * - 即: 接口污染
 * - 客户反作用力: 变更的代价无法预测
 *     如: 假设业务逻辑要求更改 timeout 签名为 timeout($timeoutId)
 */