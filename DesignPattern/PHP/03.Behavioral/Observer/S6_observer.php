<?php
/**
 * SPL 提供的观察者模式由 3 个元素组成:SplObserver, SplSubject 和 SplObjectStorage
 * SplObserver 和 SplSubject 都是接口
 * SplObjectStorage 是一个工具类, 用于更好的存储对象和删除对象
 */

class Login implements SplSubject
{
    private $storage;

    const LOGIN_USER_UNKNOWN = 1;
    const LOGIN_WRONG_PASS = 2;
    const LOGIN_ACCESS = 3;
    private $status = array();

    function __construct()
    {
        $this->storage = new SplObjectStorage();
    }

    function attach(SplObserver $observer)
    {
        $this->storage->attach($observer);
    }

    function detach(SplObserver $observer)
    {
        $this->storage->detach($observer);
    }

    function notify()
    {
        foreach ($this->storage as $obs) {
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

abstract class LoginObserver implements SplObserver
{
    private $login;

    function __construct(Login $login)
    {
        $this->login = $login;
        $login->attach($this);
    }

    function update(SplSubject $subject)
    {
        if ($subject === $this->login) {
            $this->doUpdate($subject);
        }
    }

    abstract function doUpdate(Login $login);
}

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