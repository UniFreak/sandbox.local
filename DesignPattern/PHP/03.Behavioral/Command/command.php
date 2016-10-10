<?php
/**
 * 命令模式有三部分组成, 实例化命令对象的客户端, 部署命令对象的调用者和接受命令的接收者
 *
 * 这个例子实现的框架实际上是另外一个模式--前端控制器模式的简化版本
 */

// ==================== 接收者 ====================
/**
 * 通过 CommandContext 机制, 用户请求数据可被传递给 Command 对象, 同时响应也可以被返回到视图层
 */
class CommandContext
{
    private $params = array();
    private $error = "";

    function __construct()
    {
        $this->params = $_REQUEST;
    }

    function addParam($key, $val)
    {
        $this->params[$key] = $val;
    }

    function get($key)
    {
        return $this->params[$key];
    }

    function setError($error)
    {
        $this->error = $error;
    }

    function getError()
    {
        return $this->error;
    }
}

/**
 * 命令对象的接口很简单, 他只要求实现 execute() 方法
 */
abstract class Command
{
    abstract function execute(CommandContext $context);
}

class LoginCommand extends Command
{
    /**
     * Command 对象不应该执行太多的逻辑, 他们应该负责检出输入, 处理错误, 缓存对象和调
     * 用其他对象来执行一些必要的操作. 如果你发现应用逻辑过多的出现在 Command 类中, 通
     * 常需要考虑重构代码
     */
    function execute(CommandContext $context)
    {
        $manager = Registry::getAccessManager();
        $user = $context->get('username');
        $pass = $context->get('pass');
        $userObj = $manager->login($user, $pass);
        if (is_null($userObj)) {
            $context->setError($manager->getError());
            return false;
        }
        $context->addParam("user", $userObj);
        return true;
    }
}

class FeedbackCommand extends Command
{
    function execute(CommandContext $context)
    {
        $msgSystem = Registry::getMessageSystem();
        $email = $context->get('email');
        $email = $context->get('msg');
        $email = $context->get('topic');
        $result = $msgSystem->send($email, $msg, $topic);
        if (!$result) {
            $context->setError($msgSystem->getError());
            return false;
        }
        return true;
    }
}

// ==================== 客户端 ====================
class CommandNotFoundException extends Exception {}

class CommandFactory
{
    private static $dir = 'commands';

    static function getCommand($action = 'Default')
    {
        if (preg_match('/\W/', $action)) {
            throw new Exception("illegal characters in action");
        }
        $class = UCFirst(strtolower($action)) . "Command";
        $file = self::$dir.DIRECTORY_SEPARATOR."{$class}.php";
        if (!file_exists($file)) {
            throw new CommandNotFoundException("could not find '$file'");
        }
        require_once($file);
        if (!class_exists($class)) {
            throw new CommandNotFoundException("no '$class' class located");
        }
        $command = new $class();
        return $command;
    }
}

// ==================== 调用者 ====================
class Controller
{
    private $context;

    function __construct()
    {
        $this->context = new CommandContext();
    }

    function getContext()
    {
        return $this->context;
    }

    function process()
    {
        $cmd = CommandFactory::getCommand($this->context->get('action'));
        if (!$cmd->execute($this->context)) {
            // 处理失败
        } else {
            // 处理成功
            // 分发视图
        }
    }
}

// ==================== use case ====================
$controller = new Controller();
// 伪造用户请求
$context = $controller->getContext();
$context->addParam('action', 'login');
$context->addParam('username', 'bob');
$context->addParam('pass', 'tiddles');
$controller->process();