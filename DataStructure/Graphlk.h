/**
 * 邻接表表示图
 */
#define vNum 20
typedef struct arcNode
{
    int ajxVex; // 下一条边的顶点编号
    WeightType weight; // 带权图的权值域(若非带权图, 可省略)
    struct arcNode *nextArc; // 指向下一条边的指针
} ArcNode;

typedef struct vexNode
{
    int vertext; // 顶点编号
    ArcNode *firstArc; // 指向第一条边的指针
} AdjList[vNum];

typedef struct gp
{
    AdjList adjList;
    int vexNum, arcNum; // 顶点和边的个数
} Graph;


/**
 * 建立有向图的邻接表
 */
CreateAdjList(Graph *g)
{
    int n, e, i, j, k;
    ArcNode *p;
    scanf("%d %d", n, e); // 读入顶点数和边数
    g->vexNum = n;
    g->arcNum = e;
    for (i = 0; i < n; i++) {
        g->adjList[i].vertext = i; // 初始化顶点的信息
        g->adjList[i].firstArc = NULL; // 初始化 i 的第一个邻接点为 NULL
    }
    for (k = 0; k < e; k++) { // 输入 e 条弧
        scanf("%d %d", &i, &j);
        p = (ArcNode *)malloc(sizeof(ArcNode)); // 生成 j 的表结点
        p->adjVex = j;
        p->nextArc = g->adjList[i].firstArc; // @?: 将表结点 j 链到 i 的单链表中
        g->adjList[i].firstArc = p;
    }
}

/**
 * 遍历(深度优先算法, 邻接表)
 *
 * 时间复杂度: O(n+e)
 */
Dfs(Graph g, int v)
{
    ArcNode *p;
    printf("%d", v);
    visited[v] = 1;
    p = g.adjList[v].firstArc;
    while (p != NULL) {
        if (!visited[p->adjVex]) {
            Dfs(g, p->adjVex);
        }
    }
}

/**
 * 遍历(广度优先算法, 邻接表, 链队列)
 */
Bfs(Graph g, int v)
{
    LinkQueue Q;
    ArcNode *p;
    InitQueue(&Q);
    printf("%d", v);
    visited[v] = 1;
    EnQueue(&Q, v);
    while (!EmptyQueue(Q)) {
        v = GetHead(&Q);
        OutQueue(&Q);
        p = g.adjList[v].firstArc;
        while (p != NULL) {
            if (!visited[p->adjVex]) {
                printf("%d", p->adjVex);
                visited[p->adjVex] = 1;
                EnQueue(&Q, p->adjVex);
            }
            p = p->nextArc;
        }
    }
}

/**
 * 拓扑排序算法
 */
const int vNum = 20;
typedef struct arcNode
{
    int adjVex;
    struct arcNode *nextArc;
} ArcNode;

typedef struct vexNode
{
    VertextType vertex;
    int in; // 入度域
    ArcNode *firstArc;
} AdjList[vNum];

typedef struct gp
{
    AdjList adjList;
    int vexNum, arcNum;
} Graph;

TpSort(Graph g)
{
    LinkStack *S;
    ArcNode *p;
    int m, i, v;
    InitStack(S);
    for (i = 0; i < g.vexNum; i++) {
        if (g.adjList[i].in == 0) {
            Push(S, i);
        }
    }
    m = 0;
    while (!EmptyStack(S)) {
        v = Gettop(S);
        Pop(S);
        printf("%d ", v);
        m++;
        p = g.adjList[v].firstArc;
        while (p != NULL) {
            (g.adjList[p->adjVex].in)--;
            if (g.adjList[p->adjVex].in == 0) {
                Push(S, p->adjVex);
            }
            p = p->nextArc;
        }
    }
    if (m < g.vexNum) {
        return 0; // 图中有环
    } else {
        return 1;
    }
}