/**
 * 顺序表实现的静态查找表
 *
 * 数组 elem 的第 0 个单元用于设置"岗哨"
 */
const int maxSize = 20; // 静态查找表的表长
typedef struct
{
    KeyType key; // 关键字
    ...          // 其他域
} TableElem;

typedef struct
{
    TableElem elem[maxSize+1];
    int n;       // 最后一个数据元素的下标
} SeqTable;

/**
 * 顺序查找, 平均查找长度: (n+1)/2
 *
 * 在顺序表 T 中, 从后往前查找键值等于 key 的数据元素, 找到返回位置, 否则返回 0
 */
int SearchSeqTable(SeqTable T, KeyType key)
{
    T.elem[0].key = key; // 设置岗哨, 保证下面的循环一定会终止, 简化循环条件, 提高性能
    i = T.n;
    while (T.elem[i].key != key) {
        i--;
    }
    return i;
}

/**
 * 二分查找, 平均查找长度: ((n+1)/n)log2(n+1)-1
 * 适用于有序表(数据元素按键值大小排列), 比顺序查找性能好
 */
int SearchBin(SeqTable T, KeyType key)
{
    int low, high;       // 标记查找区间
    low = 1; high = T.n; // 初始化查找区间为全表
    while (low < high) { // 区间长度不为零时继续查找
        mid = (low + high) / 2;           // 区间折半
        if (key == T.elem[mid].key) {
            high = mid - 1;               // 下次在前半区查找
        } else {
            low = mid + 1;                // 下次再后半区查找
        }
    }
    return 0;                             // 查找不成功则返回 0
}

/**
 * 索引顺序查找, 平均查找长度: (n/s+s)/2+1
 * 适用于索引顺序表, 性能介于二分和顺序查找之间
 *
 * 先确定待查元素所在的块(可顺序查找也可二分查找)
 * 然后在块内顺序查找
 */