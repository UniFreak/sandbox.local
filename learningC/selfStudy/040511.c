#include <stdio.h>

main()
{
    int i;

    printf("==================== for ====================\n");
    for (i = 1; i <= 100; i++) {
        printf("%d's square is %d\n", i, i*i);
    }

    printf("==================== do while ====================\n");
    i = 0;
    do {
        printf("%d's square is %d\n", i, i*i);
        i++;
    } while (i <= 100);

    printf("==================== while ====================\n");
    i = 0;
    while (i <= 100) {
        printf("%d's square is %d\n", i, i*i);
        i++;
    }
}