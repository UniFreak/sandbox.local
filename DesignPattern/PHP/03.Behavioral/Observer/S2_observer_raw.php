<?php
// 被观察者接口: 实现注册/解除/通知观察者
interface Observable
{
    function attach(Observer $observer);
    function detach(Observer $observer);
    function notify();
}

// 中心类
class Login implements Observable
{
    private $observers;

    const LOGIN_USER_UNKNOWN = 1;
    const LOGIN_WRONG_PASS = 2;
    const LOGIN_ACCESS = 3;
    private $status = array();

    function __constrauct()
    {
        $this->observers = array();
    }

    function attach(Observer $observer)
    {
        $this->observers[] = $observer;
    }

    function detach(Observer $observer)
    {
        $newObservers = array();
        foreach ($this->observers as $obs) {
            if ($obs !== $observer) {
                $newObservers[] = $obs;
            }
        }
        $this->observers = $newObservers;
    }

    function notify()
    {
        foreach ($this->observers as $obs) {
            $obs->update($this);
        }
    }

    function handleLogin($user, $pass, $ip)
    {
        switch (rand(1, 3)) {
            case 1:
                $this->setStatus(self::LOGIN_ACCESS, $user, $ip);
                $ret = true;
                break;
            case 2:
                $this->setStatus(self::LOGIN_WRONG_PASS, $user, $ip);
                $ret = false;
                break;
            case 3:
                $this->setStatus(self::LOGIN_USER_UNKNOWN, $user, $ip);
                $ret = false;
                break;
        }
        // 通知观察者
        $this->notify();
        return $ret;
    }

    private function setStatus($status, $user, $ip)
    {
        $this->status = array($status, $user, $ip);
    }

    function getStatus()
    {
        return $this->status;
    }

}

// 观察者接口
interface Observer
{
    function update(Observable $observable);
}

// 观察者
class SecurityMonitor implements Observer
{
    function update(Observable $observable)
    {
        $status = $observable->getStatus(); // P2: 无法保证 $observable 就是一个 Login 对象
                                            // 所以此调用时不安全的
        if ($status[0] == Login::LOGIN_WRONG_PASS) {
            print __CLASS__.":\tsending mail to sysamdin\n";
        }
    }
}

// ==================== use case ====================
$login = new Login();
$login->attach(new SecurityMonitor());
$login->handleLogin(null, null, null);