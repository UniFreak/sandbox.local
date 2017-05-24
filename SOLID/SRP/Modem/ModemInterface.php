<?php
/**
 * 问题:
 * - 无法重用接口(如 send&receive), 或导致不必要代码
 * - 客户反作用力导致接口变更(如 dial 签名) --> 只使用 send&receive 的客户也得变更
 */
interface Modem
{
    public function dial($no);
    public function hangUp();
    public function send($data);
    public function receive();
}

/**
 * 解决:
 * 分离接口(@see ISP)
 */
Interface DataChannel
{
    public function send($data);
    public function receive();
}

Interface Connection
{
    public function dial($no);
    public function hangUp();
}

Class ModemImplementation implements DataChannel, Connection
{
    public function dial($no) {}
    public function hangUp() {}
    public function send($data) {}
    public function receive() {}

}

/**
 * 真有必要吗?
 * - 取决于应用程序变化方式
 * - SRP 中的职责即: 变化的原因
 *     + 如果导致这些操作更改的变化原因不止一个 -> 有必要
 *     + 如果导致这些操作更改的变化原因只有一个 -> 没必要
 */