/**
 * 孩子链表树
 */
const int MAXND = 20;

typedef struct bNode
{
    int child;
    struct bNode *next;

} node, *childLink;

typedef struct
{
    DataType data;
    childLink hp;
} headNode;

headNode link[MAXND];

/**
 * 孩子兄弟链表树
 */
typedef struct tNode
{
    DataType data;
    struct tNode *son, *brother;
} *Tree;

/**
 * 双亲链表树
 */
const int size = 10;
typedef struct {
    DataType data;
    int parent;
} Node;

Node slist[size];

/**
 * 二叉链表二叉树
 */
typedef struct btNode
{
    DataType data;
    struct btNode *lchild, *rchild;
} *BinTree;

BinTree root;

/**
 * 三叉链表二叉树
 */
typedef struct ttNode
{
    dataType data;
    struct ttNode *lchild, *parent, *rchild;
} *TBinTree;

TBinTree root;


/**
 * 先序遍历(递归实现)
 */
void preorder(BinTree bt)
{
    if (bt != NULL) {
        visit(bt);
        preorder(bt->lchild);
        preorder(bt->rchild);
    }
}

/**
 * 先序遍历(非递归实现)
 */
void preorder(BintTree bt)
{
    BinTree p;
    LinkStack *LS;
    if (bt == NULL) {
        return;
    }
    InitStack(LS);
    p = bt;
    while (p != NULL || !EmptyStack(LS)) {
        if (p != NULL) {
            Visit(p->data);
            Push(LS, p);
            p = p->lchild;
        } else {
            p = Gettop(LS);
            Pop(LS);
            p = p->rchild;
        }
    }
}

/**
 * 中序遍历(递归实现)
 */
void inorder(BintTree bt)
{
    if (bt != NULL) {
        inorder(bt->lchild);
        visit(bt);
        inorder(bt->rchild);
    }
}

/**
 * 中序遍历(非递归实现)
 */
 void inorder(BintTree bt)
 {
     BinTree p;
     LinkStack *LS;
     if (bt == NULL) {
         return;
     }
     InitStack(LS);
     p = bt;
     while (p != NULL || !EmptyStack(LS)) {
         if (p != NULL) {
             Push(LS, p);
             p = p->lchild;
         } else {
             p = Gettop(LS);
             Pop(LS);
             Visit(p->data); // 与先序遍历(非递归实现)的唯一不同是这个操作的时机
             p = p->rchild;
         }
     }
 }

/**
 * 后序遍历(递归实现)
 */
void postorder(BinTree bt)
{
    if (bt != NULL) {
        postorder(bt->lchild);
        postorder(bt->rchild);
        visit(bt);
    }
}

/**
 * 后序遍历(非递归实现): 较复杂, 从略...
 */

/**
 * 层次遍历
 */
void levelorder(BinTree bt)
{
    LinkQueue Q;
    InitQueue(&Q);
    if (bt != NULL) {
        EnQueue(&Q, bt);
        while (!EmptyQueue(Q)) {
            p = GetHead(&Q);
            outQueue(&Q);
            visit(p);
            if (p->lchild != NULL) {
                EnQueue(&Q, p->lchild);
            }
            if (p->rchild != NULL) {
                EnQueue(&Q, p->rchild);
            }
        }
    }
}

/**
 * 利用遍历求高度
 */
int Height(BinTree bt)
{
    int lh, rh;
    if (bt == NULL) {
        return 0;
    } else {
        lh = Height(bt->lchild);
        rh = Height(bt->rchild);
        return 1 + (lh > rh ? lh : rh);
    }
}

/**
 * 构造树(根据先序和中序序列)
 *
 * a[] 存储先序序列, b[] 存储中序序列
 * i, j 是先序序列的下标上下界, m, n 是中序序列的下标上下界
 */
BinTree Create(char a[], char b[], int i, int j, int m, int n)
{
    int k;
    BinTree p;
    if (n < 0) {
        return NULL;
    }
    p = malloc(sizeof(BinTree));
    p->data = a[i];
    k = m;
    // 在中序序列中找根
    while ((k <= n) && (b[k] != a[i])) {
        k++;
    }
    if (k > n) {
        error();
    }
    p->lchild = Create(a, b, i+1, i+k-m, m, k-1); // 递归构造左子树
    p->rchild = Create(a, b, i+k-m+1, j, k+1, n); // 递归构造右子树
    return p;
}

/**
 * 构造树(根据后序和中序序列)
 */