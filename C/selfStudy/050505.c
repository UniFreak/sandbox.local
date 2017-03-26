#include <stdio.h>

main()
{
    int m[3][5], i, k;
    int max, min, max_i, max_k, min_i, min_k;
    for (i = 0; i < 3; i++) {
        for (k = 0; k < 5; k++) {
            scanf("%d", &m[i][k]);
        }
    }

    max = min = m[0][0];
    max_i = max_k = min_i = min_k = 0;
    for (i = 0; i < 3; i++) {
        for (k = 0; k < 5; k++) {
            if (max < m[i][k]) {
                max = m[i][k];
                max_i = i; max_k = k;
            }

            if (min > m[i][k]) {
                min = m[i][k];
                min_i = i; min_k = k;
            }
        }
    }

    printf("the max is %d in line %d, column %d; the min is %d in line %d, colum %d",
        max, max_i + 1, max_k + 1,
        min, min_i + 1, min_k + 1);
}