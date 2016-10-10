<?php
// ==================== Observable ====================
interface Observable
{
    function attach(Observer $observer);
    function detach(Observer $observer);
    function notify();
}

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

// ==================== Observer ====================
interface Observer
{
    function update(Observable $observable);
}

// 添加 LoginObserver 类, 负责确保主体是正确的类型
abstract class LoginObserver implements Observer
{
    private $login;

    function __construct(Login $login)
    {
        $this->login = $login;
        $login->attach($this);
    }

    function update(Observable $observable)
    {
        if ($observable === $this->login) {
            $this->doUpdate($observable); // 模板方法模式, 保证 update 接口的可用性
        }
    }

    abstract function doUpdate(Login $login);
}

// 观察者实现各自的 doUpdate 的模板方法
class SecurityMonitor extends LoginObserver
{
    function doUpdate(Login $login)
    {
        $status = $login->getStatus();
        if ($status[0] == Login::LOGIN_WRONG_PASS) {
            print __CLASS__.":\tsending mail to sysamdin\n";
        }
    }
}

class GeneralLogger extends LoginObserver
{
    function doUpdate(Login $login)
    {
        $status = $login->getStatus();
        print __CLASS__.":\tadding login data to log\n";
    }
}

class PartnershipTool extends LoginObserver
{
    function doUpdate(Login $login)
    {
        $status = $login->getStatus();
        print __CLASS__.":\tset cocokie if IP matches the list\n";
    }
}


// ==================== use case ====================
$login = new Login();
new SecurityMonitor($login);
new GeneralLogger($login);
new PartnershipTool($login);

$login->handleLogin(null, null, null);