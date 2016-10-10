<?php
/**
 * 本例中, 我们需要在用户登录的时候, 进行各种不同的操作, 比如进行日志记录, 邮件发送等.
 * 这些操作可能会随着需求变化而随时变化
 */

class Login
{
    const LOGIN_USER_UNKNOWN = 1;
    const LOGIN_WRONG_PASS = 2;
    const LOGIN_ACCESS = 3;
    private $status = array();

    function handleLogin($user, $pass, $ip)
    {
        switch (rand(1, 3)) {
            case 1:
                $this->setStatus(self::LOGIN_ACCESS, $user, $ip);
                $ret = ture;
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

        // 需求来了: 记录日志
        Logger::logIP($user, $ip, $this->getStatus());
        // 需求来了: 登录失败后发送邮件
        if (!$ret) {
            Notifier::mailWarning($user, $ip, $this->getStatus());
        }

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

/**
 * P1: 破坏我们的设计
 *     Login 紧紧嵌入到这个特定系统, 如果没有逐行检查代码, 然后移除特别针对旧系统的代码
 *     则很难把它提取出来放到其他产品中.
 *     即使这样做了, 维护并同步两个系统中看起来相似又有所不同的 Login 类也是一种折磨
 */