<?php
class Rectangle
{
    private $topLeft;
    private $width;
    private $height;

    public function setWidth($newWidth)
    {
        $this->width = $newWidth;
    }

    public function setHeight($newHeight)
    {
        $this->height = $newHeight;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function area()
    {
        return $this->width * $this->height;
    }
}

/**
 * Square **IS-A** rectangle, 所以...
 */
class Square extends Rectangle {}
/**
 * 问题:
 * 1. 对于 Square 来说, 不需要把宽高分开 --> 性能问题, 暂不考虑
 * 2. 分开设置宽高, 对 Square 来说并不合适 --> @see Square2
 */

class Square2 extends Rectangle
{
    public function setWidth($newWidth)
    {
        parent::setWidth($newWidth);
        parent::setHeight($newWidth);
    }

    public function setHeight($newHeight)
    {
        parent::setWidth($newHeight);
        parent::setHeight($newHeight);
    }
}
/**
 * 结果: 保证了 Square 的不变形, 自相容的设计
 * 问题:
 * 3. 自相容并不代表与客户代码相容 --> @see Shape
 */
function client(Rectangle $r)
{
    $r->setWidth(4);
    $r->setHeight(5);
    assert('$r->area() == 20');
}
client(new Square2());
/**
 * 客户代码的假设完全合情合理, 但是使用 Square2 代替 Rectangle 却导致错误
 * - 违反了 LSP
 * - 一个设计模型的有效性只能通过他的客户程序来表现, 自相容不代表真正有效
 */

/**
 * 提取公共部分(如 Shape 类), 父子变兄弟
 */