#include <stdio.h>

main()
{
    int matrix[5][5], i, j, yes = 1;

    for (i = 0; i < 5; i++) {
        for (j = 0; j < 5; j++) {
            scanf("%d", &matrix[i][j]);
        }
    }

    for (i = 0; i < 3; i++) {
        for (j = 0; j < 3; j++) {
            if (matrix[i][j] != matrix[j][i]) {
                yes = 0;
                break;
            }
        }
    }

    if (yes) {
        printf("yes!");
    } else {
        printf("no!");
    }
}