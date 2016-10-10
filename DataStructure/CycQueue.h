/**
 * 循环队列: 把队列想象为首尾相连, 已解决 "假溢出"
 *
 * 但是这样做, 会导致无法区分队列满和队列空两种情况(此时都有 front == rear)
 * 所以规定只剩一个单元式就认为队列满(此时队列尾指针只差一步就追上队列首指针)
 */

// ==================== 定义 ====================
const int maxSize = 20;
typedef struct cycQueue
{
    DataType data[maxsize];
    int front, rear;
} CycQueue;

// ==================== 运算 ====================
/**
 * 初始化
 */
void InitQueue(CycQueue CQ)
{
    CQ.front = 0;
    CQ.rear = 0;
}
/**
 * 判队列空
 */
int EmptyQueue(CycQueue CQ)
{
    if (CQ.rear == CQ.front) {
        return 1;
    } else {
        return 0;
    }
}
/**
 * 入队列
 */
int EnQueue(CycQueue CQ, DataType x)
{
    if ((CQ.rear + 1) % maxSize == CQ.front) {
        error("queue is full");
        return 0;
    } else {
        CQ.rear = (CQ.rear + 1) % maxSize;
        CQ.data[CQ.rear] = x;
        return 1;
    }
}
/**
 * 出队列
 */
int OutQueue(CycQueue CQ)
{
    if (EmptyQueue(CQ)) {
        error("queue is empty");
        return 0;
    } else {
        CQ.front = (CQ.front + 1) % maxSize;
        return 1;
    }
}
/**
 * 取队列首元素
 */
DataType GetHead(CycQueue CQ)
{
    if (EmptyQueue(CQ)) {
        return NULLData;
    } else {
        return CQ.data[(CQ.front + 1) % maxSize];
    }
}