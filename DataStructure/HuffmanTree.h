/**
 * 哈夫曼树: 平均比较次数最小的判定树
 */
const int n = 10;

typedef struct
{
    float w; // 权值
    int parent, lchild, rchild;
} node;

typedef node hftree[2*n-1];


/**
 * 哈夫曼算法构造哈夫曼树
 *
 * k: 给定的权值个数
 * w: 权值数组
 * T: 哈夫曼树
 */
void Huffman(int k, float w[], hftree T)
{
    int i, j, x, y;
    float mn, n;
    for (i = 0; i < 2*k - 1; i++) {
        T[i].parent = -1;
        T[i].lchild = -1;
        T[i].rchild = -1;
        if (i < k) {
            T[i].w = w[i];
        } else {
            T[i].w = 0;
        }
    }

    for (i = 0; i < k-1; i++) {
        x = 0;
        y = 0;
        m = MAX;
        n = MAX;
        for (j = 0; j < k+i; j++) {
            if ((T[j].w < m) && (T[j].parent == -1)) {
                n = m;
                y = x;
                m = T[j].w;
                x = j;
            } else if ((T[j].w < n) && (T[j].parent == -1)) {
                n = T[j].w;
                y = j;
            }
            T[x].parent = k+i;
            T[y].parent = k+i;
            T[k+i].w = m+n;
            T[k+i].lchild = x;
            T[k+i].rchild = y;
        }
    }
}