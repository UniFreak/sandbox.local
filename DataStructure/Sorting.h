/**
 * 待排序数据
 */
typedef struct
{
    int key;
    ItemType otherItem;
} RecordType;

typedef RecordType List[n+1];

/**
 * ==================== 插入排序 ====================
 *
 * 常用的有: 直接插入排序, 折半插入排序, 表插入排序和希尔排序
 */

/**
 * 直接插入排序算法
 *
 * 时间复杂度: O(n^^2), 空间复杂度: O(1)
 * 稳定
 */
void StraightInsertSort(List R, int n)
{
    int i, j;for (i = 2; i <= n; i++) {
        R[0] = R[i];       // 设置岗哨
        j = i - 1;
        while (R[0].key < R[j].key) {
            R[j+1] = R[j]; // 后移
            j--;
        }
        R[j+1] = R[0];
    }
}

/**
 * ==================== 交换排序 ====================
 *
 * 常用的有: 冒泡排序, 快速排序,
 */

/**
 * 冒泡排序算法
 *
 * 时间复杂度: O(n^^2)
 * 稳定
 */
void BubbleSort(List R, int n)
{
    int i, j, temp, endSort;
    for (i = 1; i <= n - 1; i++) {
        endSort = 0;
        for (j = 1; j <= n-i-1; j++) {
            if (R[j].key > R[j+1].key) {
                temp = R[j];
                R[j] = R[j+1];
                R[j+1] = temp;
                endSort = 1;
            }
        }
        if (endSort == 0) { // 未发生交换, 证明已经排好序, 不用再冒泡
            break;
        }
    }
}

/**
 * 快速排序
 *
 * 不稳定
 */
int QuickPartition(List R, int low, int high)
{
    x = R[low];
    while (low < high) { // 把比 x 小的所有记录移到低端, 高的移到高端
        while ((low < high) && (R[high].key >= x.key)) {
            high--;      // 从高位到低位, 找到第一个比 x 小的记录...
        }
        R[low] = R[high];// ...移动到低端
        while ((low < high) && (R[low].key <= x.key)) {
            low--;       // 从低位到高位, 找到第一个比 x 大的记录...
        }
        R[high] = R[low];// ...移动到高端
        R[low] = x;
        return low;
    }
}

void QuickSort(List R, int low, int high)
{
    if (low < high) {
        temp = QuickPartition(R, low, high);
        QuickPartition(R, low, temp-1);
        QuickPartition(R, temp+1, high);
    }
}

/**
 * ==================== 选择排序 ====================
 *
 * 常用的有: 直接选择排序, 堆选择排序
 */

/**
 * 直接选择排序算法
 *
 * 时间复杂度: O(n^^2)
 * 不稳定
 */
void SelectSort(List R, int n)
{
    int min, i, j;
    for (i = 1; i <= n-1; i++) {
        min = i;
        for (j = i+1; j <= n; j++) {
            if (R[j].key < R[min].key) {
                min = j;
            }
        }
        if (min != i) {
            swap(R[min], R[i]);
        }
    }
}

/**
 * 堆排序算法
 *
 * 平均时间: O(nLong2n)
 * 不稳定
 */
void Sift(List R, int k, int m)
{
    int i, j, x;
    List t;
    i = k;
    j = 2*i;
    x = R[k].key;
    t = R[k];
    while (j <= m) {
        if ((j < m) && (R[j].key > R[j+1].key)) {
            j++;
        }
        if (x < R[j].key) {
            break;
        } else {
            R[i] = R[j];
            i = j;
            j = 2*i;
        }
    }
    R[i] = t;
}

void HeapSort(List R)
{
    int i;
    for (i = n/2; i >= 1; i--) {
        Sift(R, i, n);
    }
    for (i = n; i >= 2; i--) {
        swap(R[1], R[i]);
        Sift(R, 1, i-1);
    }
}

/**
 * ==================== 归并排序 ====================
 *
 * 常用算法: 二路归并排序
 */

 /**
  * 二路归并排序
  *
  * 时间复杂度: O(nlog2n)
  * 稳定
  */
void Merge(List a, List R, int h, int m, int n)
{
    k = h;
    j = m+1;
    while ((h <= m) && (j <= n)) {
        if (a[h].key <= a[j].key) {
            R[k] = a[h];
            h++;
        } else {
            R[k] = a[j];
            j++;
        }
        k++;
    }
    while (h <= m) {
        R[k] = a[h];
        h++;
        k++;
    }
    while (j <= n) {
        R[k] = a[j];
        j++;
        k++;
    }
}

void MergePass(List a, List b, int n, int h)
{
    i = 1;
    while (i <= n-2*h+1) {
        merge(a, b, i, i+h-1, i+2*h-1);
        i+= 2*h;
    }
    if (i+h-1 < n) {
        merge(a, b, i, i+h-1, n);
    } else {
        for (t = i; t <= n; t++) {
            b[t] = a[t];
        }
    }
}

void MergeSort(List a, int n)
{
    m = 1;
    while (m < n) {
        MergePass(a, b, n, m);
        m = 2*m;
        MergePass(b, a, n, m);
        m = 2*m;
    }
}