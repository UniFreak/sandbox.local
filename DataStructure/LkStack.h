/**
 * 链栈: 用呆头结点的单链表实现
 *
 * 首节点是栈顶结点, 尾节点是栈底节点(前插)
 */

// ==================== 定义 ====================
typedef struct node
{
    DataType data;
    struct node *next;
} LinkStack;

// ==================== 运算 ====================
/**
 * 初始化
 */
void InitStack(LinkStack *stk)
{
    stk = (LinkStack *)malloc(sizeof(LinkStack));
    stk->next = NULL;
}

/**
 * 判栈空
 */
int EmptyStack(LinkStack *stk)
{
    if (stk->next == NULL) {
        return 1;
    } else {
        return 0;
    }
}

/**
 * 进栈
 */
int Push(LinkStack *stk, DataType x)
{
    LinkStack *temp;
    temp = (LinkStack *)malloc(sizeof(LinkStack));
    temp->data = x;
    temp->next = stk->next;
    stk->next = temp;
}

/**
 * 出栈
 */
int Pop(LinkStack *stk)
{
    LinkStack *temp;
    if (!EmptyStack(stk)) {
        temp = stk->next;
        stk->next = temp->next;
        free(temp);
        return 1;
    } else {
        return 0;
    }
}

/**
 * 取栈顶元素
 */
DataType GetTop(LinkStack *stk)
{
    if (!EmptyStack(stk)) {
        return stk->next->data;
    } else {
        return NULLData;
    }
}