<?php
/**
 * 该例中, 设计一个 MarkLogic 迷你语言, 用于让用户通过一个例如
 * `$input equals "4" or $input equals "four"`
 * 这样的语句来标识一个问答的正确答案
 *
 * 这个迷你语言的元素可以分为:
 * - 表达式
 *   - 变量($input)
 *   - 字符串("4" 或者 "four")
 *   - 运算符表达式
 *     - 布尔与(and)
 *     - 布尔或(or)
 *     - 相等测试(equals)
 */

/**
 * 为 Expression 对象提供共享的数据存储
 */
class InterpreterContext
{
    private $expresssionStore = array(); // 表达式容器

    /**
     * 存储表达式解释结果
     */
    function replace(Expression $exp, $value)
    {
        $this->expresssionStore[$exp->getKey()] = $value;
    }

    /**
     * 查看表达式解释结果
     */
    function lookup(Expression $exp)
    {
        return $this->expresssionStore[$exp->getKey()];
    }
}

/**
 * 表达式基类
 *
 * 解释表达式, 并把解释结果存储到 InterpreterContext 中(interpret)
 * 提供自身在 InterpreterContext 的索引方式(getKey)
 */
abstract class Expression
{
    private static $keycount = 0;
    private $key;

    abstract function interpret(InterpreterContext $context);

    function getKey()
    {
        if (!isset($this->key)) {
            self::$keycount++; // 默认是递增的数值
            $this->key = self::$keycount;
        }
        return $this->key;
    }
}

/**
 * 字符串表达式
 */
class LiteralExpression extends Expression
{
    private $value;

    function __construct($value)
    {
        $this->value = $value;
    }

    function interpret(InterpreterContext $context)
    {
        $context->replace($this, $this->value);
    }
}

class VariableExpression extends Expression
{
    private $name;
    private $val;

    function __construct($name, $val = null)
    {
        $this->name = $name;
        $this->val = $val;
    }

    function interpret(InterpreterContext $context)
    {
        if (!is_null($this->val)) {
            $context->replace($this, $this->val);
            $this->val = null; // 防止在同变量名的 VariableExpression 的另一个实例改变
                               // InterpreterContext 中的值后, 该实例的 interpret() 被
                               // 再次调用
        }
    }

    function setValue($value)
    {
        $this->val = $value;
    }

    /**
     * 覆写默认的 getKey 实现, 使用变量名作为索引
     */
    function getKey()
    {
        return $this->name;
    }
}


/**
 * 运算符表达式基类
 *
 * 这里用到了组合模式(OperatorExpression 既继承自 Expression, 又包含 Expression 对象)
 * doInterpret 用到了模板方法模式
 */
abstract class OperatorExpression extends Expression
{
    protected $l_op;
    protected $r_op;

    function __construct(Expression $l_op, Expression $r_op)
    {
        $this->l_op = $l_op;
        $this->r_op = $r_op;
    }

    function interpret(InterpreterContext $context)
    {
        $this->l_op->interpret($context);
        $this->r_op->interpret($context);
        $result_l = $context->lookup($this->l_op);
        $result_r = $context->lookup($this->r_op);
        $this->doInterpret($context, $result_l, $result_r);
    }
}

class EqualsExpression extends OperatorExpression
{
    protected function doInterpret(
        InterpreterContext $context,
        $result_l, $result_r)
    {
        $context->replace($this, $result_l == $result_r);
    }
}

class BooleanOrExpression extends OperatorExpression
{
    protected function doInterpret(
        InterpreterContext $context,
        $result_l, $result_r)
    {
        $context->replace($this, $result_l || $result_r);
    }
}

class BooleanAndExpression extends OperatorExpression
{
    protected function doInterpret(
        InterpreterContext $context,
        $result_l, $result_r)
    {
        $context->replace($this, $result_l && $result_r);
    }
}


/**
 * 用例:
 * 这里设用户输入 `$input equals "4" or $input equals "four"`
 */
$context = new InterpreterContext();
$input = new VariableExpression('input');
$statement = new BooleanOrExpression(
    new EqualsExpression($input, new LiteralExpression('four')),
    new EqualsExpression($input, new LiteralExpression('4'))
    );

// 设答题者输入的是 "four", "4", "52", 分别检测是否正确
foreach (array("four", "4", "52") as $val) {
    $input->setValue($val);
    print("$val:\n");
    $statement->interpret($context);
    if ($context->lookup($statement)) {
        print "top marks\n";
    } else {
        print "dunce hat on\n\n";
    }
}