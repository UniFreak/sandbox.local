/**
 * 顺序队列
 */

// ==================== 定义 ====================
/**
 * 由一个一维数组及两个分别指示队列首和队列尾的变量组成, 成为队列首指针和队列尾指针
 * 为方便操作, front 指向首元素前一个单元, rear 指向实际的队列尾元素单元
 *
 * 会产生"假溢出", 可用循环队列(CycQueue.h)解决
 */
const int maxSize = 20;
typedef struct seqQueue
{
    DataType data[maxsize];
    int front, rear;
} SeqQueue;