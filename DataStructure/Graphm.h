/**
 * 邻接矩阵表示图
 */
const int vNum = 20;
typedef struct gp
{
    VertexType vexs[vNum];
    int arcs[vNum][vNum];
    int vexNum, arcNum;
} Graph;

/**
 * 邻接矩阵表示带权图
 */
const int vNum = 20;
const int MAX_INT = 32767;
typedef struct gp
{
    VertextType vexs[vNum];
    WeightType arcs[vNum][vNum];
    int vexNun, arcNum;
} WGraph;


/**
 * 无向带权图邻接矩阵建立方法
 */
void CreateGraph(Graph *g)
{
    int i, j, n, e, w;
    char ch;
    scanf("%d %d", &n, &e); // 读入顶点个数和边树
    g->vexNum = n;
    g->arcNum = e;
    for (i = 0; i < g->vexNum; i++) {
        scanf("%c", &ch); // 读入顶点信息
        g->vexs[i] = ch;
    }
    for (i = 0; i < g->vexNum; i++) {
        for (j = 0; j < g->vexNum; j++) {
            g->arcs[i][j] = MAX_INT;  // 初始化邻接矩阵: 所有边的权均为无限大
        }
    }
    for (k = 0; k < g->arcNum; k++) {
        scanf("%d %d %d", &i, &j, &w); // 读入边(顶点对)和权值
        g->arcs[i][j] = w;
        g->arcs[j][i] = w;
    }
}

/**
 * 遍历(深度优先算法, 邻接矩阵)
 *
 * 时间复杂度: O(n^^2)
 */
Dfs(Graph *g, int v)
{
    int j;
    printf("%d", v);
    visited[v] = 1;
    for (j = 0; j < n; j++) {
        m = g->arcs[v][j];
        if (m && !visited[j]) {
            Dfs(g, j);
        }
    }
}

/**
 * 遍历(广度优先算法, 邻接矩阵, 链队列)
 */
Bfs(Graph g, int v)
{
    LinkQueue Q;
    int j;
    InitQueue(&Q);
    printf("%d", v);
    visited[v] = 1;
    EnQueue(&Q, v);
    while (!EmptyQueue(Q)) {
        v = GetHead(&Q);
        OutQueue(&Q);
        for (j = 0; j < n; j++) {
            m = g->arcs[v][j];
            if (m && !visited[j]) {
                printf("%d", j);
                visited[j] = 1;
                EnQueue(&Q, j);
            }
        }
    }
}

/**
 * Prim 算法求最小生成树
 *
 * ?: 由于书的印刷问题, 不确定代码是否正确(缩进)
 * ?: 读不懂
 */
const int MAX_INT = 32767;
typedef struct gp
{
    int vexs[vNum];
    int arcs[vNum][vNum];
    int vexNum, arcNum;
} Graph;

struct
{
    int adjVex;
    int lowCost;
} closeEdge[vNum];

Prim(Graph g, int u)
{
    int v, k, j, min;
    for (v = 0; v < g.vexNum; v++) {
        if (v != u) {
            closeEdge[v].adjVex = u;
            closeEdge[v].lowCost = g.arcs[u][v];
        }
    }
    closeEdge[u].lowCost = MAX_INT;

    for (k = 0; k < g.vexNum; k++) {
        min = closeEdge[k].lowCost;
        v = k;
        for (j = 0; j < g.vexNum; j++) {
            if (closeEdge[j].lowCost < min) {
                min = closeEdge[j].lowCost;
                v = j;
            }
        }
        printf("%d %d\n", closeEdge[v].adjVex, v);
        closeEdge[v].lowCost = MAX_INT;
        for (j = 0; j < g.vexNum; j++) {
            if (g.arcs[v][j] < closeEdge[j].lowCost) {
                closeEdge[j].lowwCost = g.arcs[v][j];
                closeEdge[j].adjVex = v;
            }
        }
    }
}