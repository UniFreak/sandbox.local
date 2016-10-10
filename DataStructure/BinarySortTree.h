/**
 * 二叉排序树是一种特殊的二叉树, 他的存储结构及类型定义与二叉树相同
 * 这种树表的结构本身是在查找过程中动态生成的(查找不成功则插入)
 */
typedef struct btNode
{
    KeyType key;
    struct btNode *lchild, *rchild;
} BSTNode, *BinTree;

BinTree bst;

/**
 * 查找
 *
 * 二叉排序树上的查找长度不仅与结点数 n 有关, 也与二叉排序树的生成过程有关
 * 介于 O(n) 和 O(log2N) 之间
 */
BinTree SearchBST(BinTree bst, KeyType key)
{
    if (bst == NULL) {
        return NULL; // 不成功返回 NULL
    } else if (key == bst->key) {
        return bst;  // 成功返回结点地址
    } else if (key < bst->key) {
        return SearchBST(bst->lchild, key); // 若小于当前结点的 key, 则在左子树中递归查找
    } else {
        return SearchBST(bst->rchild, key); // 若大于当前结点的 key, 则在右子树中递归查找
    }
}

/**
 * 插入
 *
 * 根据二叉排序树的动态生成性质, 插入算法必须包含查找过程
 * 可把上面的查找算法稍加修改, 以应用到插入算法中
 *
 * 新增 *f 参数, 指向查到节点的双亲
 */
 BinTree SearchBST(BinTree bst, KeyType key, BSTNode *f)
 {
     if (bst == NULL) {
         return NULL;
     } else if (key == bst->key) {
         return bst;
     } else if (key < bst->key) {
         return SearchBST(bst->lchild, key, bst);// 把当前 bst 作为下次的 *f 递归在左子树查找
     } else {
         return SearchBST(bst->rchild, key);     // 把当前 bst 作为下次的 *f 递归在右子树查找
     }
 }

int InsertBST(BinTree bst, KeyType key)
{
    BSTNode *p, *t, *f;
    f = NULL;
    t = SearchBST(bst, key, f);
    if (t == NULL) {
        p = malloc(sizeof(btNode));
        p->key = key;
        p->lchild = NULL;
        p->rchild = NULL;
        if (f == NULL) {
            bst = p;        // 被插入结点 p 为新的根节点
        } else if (key < f->key) {
            f->lchild = p;  // 被插入结点  p 为 f 的左孩子
        } else {
            f->rchild = p;  // 被插入结点  p 为 f 的右孩子
        }
        return 1;
    } else {
        return 0;
    }
}