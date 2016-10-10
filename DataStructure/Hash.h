// ==================== LInkHash ====================
/**
 * 链地址法散列表
 */
const int n = 20;  // 表长
typedef struct TagNode
{
    KeyType key;           // 关键字
    struct TagNode *next;  // 同义词子表中下一个同义词
    ...                    // 其他域
} *Pointer, Node;

typedef Pointer LinkHash[n];


/**
 * 查找
 */
Pointer SearchLinkHash(KeyType key, LinkHash HP)
{
    i = H(key);        // 计算 i 的散列地址
    p = HP[i];         // 找到同义词表头
    if (p == NULL) {
        return NULL;
    }
    while ((p != NULL) && (p->key != key)) { // 在同义词中查找
        p = p->next;
    }
    return p;
}

/**
 * 插入: 查找不成功则插入
 */
void InsertLinkHash(KeyType key, LinkHash HP)
{
    if ((SearchLinkHash(key, HP)) == NULL) {
        i = H(key);
        q = Pointer malloc(sizeof(Node));
        q->key = key;
        q->next = HP[i];  // 前插
        HP[i] = 1;
    }
}

/**
 * 删除: 查找成功则删除
 */
void DeleteLinkHash(KeyType key, LinkHash HP)
{
    i = H(key);
    if (HP[i] == NULL) {
        return;
    } else {
        p = HP[i];
        if (p->key == key) {
            HP[i] = p->next;
            free(p);
            return;
        } else {
            while (p->next != NULL) {
                q = p;
                p = p->next;
                if (p->key == key) {
                    q->next = p->next;
                    free(p);
                    return;
                }
            }
        }
    }
}


// ==================== OpenHash ====================
/**
 * 线性探测法散列表
 */
const int maxSize = 20;
typedef struct
{
    KeyType key;
    ...
} Element;
typedef Element OpenHash[maxSize];

/**
 * 查找
 */
int SearchOpenHash(KeyType key, OpenHash HL)
{
    d = H(key);
    i = d;
    while ((HL[i].key != NULLkey) && (HL[i].key != key)) {
        i = (i+1) % m;
        if (HL[i].key == key) {
            return i;
        } else {
            return 0;
        }
    }
}