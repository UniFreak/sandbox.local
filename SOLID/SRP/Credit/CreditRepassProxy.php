<?php
/**
 * @see credit.youxinjinrong.com/personCreditRepo --> 简化后
 * 问题:
 * - 业务规则和持久化操作两个职责混在一起
 * - 领域操作难以重用
 * - 其中一个概念的更改影响另一个概念
 * - 违反 DIP: 高层策略依赖于低层存储机制
 */
class Credit
{
    private $id;
    private $status;

    public function repass()
    {
        // update credit set status='processing' where id={$this->id}
    }
}

/**
 * 解决:
 * - 使用 proxy 模式
 * - 设计: 使用委托
 */
Interface Credit
{
    public function repass();
}

class CreditImplementation implements Credit
{
    private $id;
    private $status;

    public function repass() {
        $this->status = 'processing';
    }
}

class DB
{
    public static function updateStatus($id, $newStatus)
    {
        // "update credit set status=$newStatus where id=$id";
    }
}

class CreditDBProxy implements Credit
{
    private $itsImplementation;

    /**
     * 注意:
     * - 设想中的方案是委托给 CreditImplementation
     * - 实现时发现并不能那么做
     * - 创建的代理完全就是为了分离数据库实现和业务规则
     */
    public function repass()
    {
        DB::updateStatus($id, $newStatus = 'processing');
    }
}

/**
 * 有必要吗?
 * - 这只是一个简化实例, 看起来把问题复杂化了
 */