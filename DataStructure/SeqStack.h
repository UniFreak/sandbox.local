/**
 * 顺序栈: 用一组连续的存储单元依次存放栈中的每个元素, 并用始端作为栈底
 */

// ==================== 定义 ====================
/**
 * 通常用一个一维数组和一个记录栈顶位置的变量来实现
 */
const int maxSize = 6;
typedef struct seqStack
{
    DataType data[maxSize];
    int top;
} SeqStack;

// ==================== 运算 ====================
/**
 * 初始化
 */
int InitStack(SeqStack *stk)
{
    stk->top = 0;
    return 1;
}

/**
 * 判栈空
 */
int EmptyStack(SeqStack *stk)
{
    if (stk->top = 0) {
        return 1;
    } else {
        return 0;
    }
}

/**
 * 进栈
 */
int Push(SeqStack *stk, DataType x)
{
    if (stk->top == maxSize - 1) {
        error("stack full");
        return 0;
    } else {
        stk->top++;
        stk->data[stk->top] = x;
        return 1;
    }
}

/**
 * 出栈
 */
int Pop(SeqStack *stk)
{
    if (EmptyStack(stk)) {
        error("underflow");
        return 0;
    } else {
        stk->top--;
        return 1;
    }
}

/**
 * 取栈顶元素
 */
DataType GetTop(SeqStack *stk)
{
    if (EmptyStack(stk)) {
        return NULLData;
    } else {
        return stk->data[stk->top];
    }
}