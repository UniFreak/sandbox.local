/**
 * 链队列: 使用带有头结点的单链表
 *
 * 头结点的 next 指向队列首节点, 尾指针指向队列尾结点
 */

// ==================== 定义 ====================
typedef struct LinkQueueNode
{
    DataType data;
    struct LinkQueueNode *next;
} LkQueueNode;

typedef struct LinkQueue
{
    LinkQueueNode *front, *rear;
} LinkQueue;

// ==================== 运算 ====================
/*
 * 初始化
 */
void InitQueue(LinkQueue *LQ)
{
    LinkQueueNode *temp;
    temp = (LinkQueueNode *)malloc(sizeof(LinkQueueNode));
    LQ->front = temp;
    LQ->rear = temp;
    (LQ->front)->next = NULL;
}
/**
 * 判队列空
 */
int EmptyQueue(LinkQueue LQ)
{
    if (LQ.rear == LQ.front) {
        return 1;
    } else {
        return 0;
    }
}
/**
 * 入队列
 */
int EnQueue(LinkQueue *LQ, DataType x)
{
    LinkQueueNode *temp;
    temp = (LinkQueueNode *)malloc(sizeof(LinkQueueNode));
    temp->data = x;
    temp->next = NULL;
    (LQ->rear)->next = temp;
    LQ->rear = temp;
}
/**
 * 出队列
 */
int OutQueue(LinkQueue *LQ)
{
    LinkQueueNode *temp;
    if (EmptyQueue(LQ)) {
        error("queue is empty");
        return 0;
    } else {
        temp = (LQ->front)->next;
        (LQ->front)->next = temp->next;
        if (temp->next == NULL) {
            LQ->rear = LQ->front;
        }
        free(temp);
        return 1;
    }
}
/**
 * 取队列首元素
 */
DataType GetHead(LinkQueue LQ)
{
    LinkQueueNode *temp;
    if (EmptyQueue(LQ)) {
        return NULLData;
    } else {
        temp = LQ.front->next;
        return temp->data;
    }
}