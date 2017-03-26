#include <stdio.h>

main()
{
    int a[10], tmp, i;
    for (i = 0; i < 10; i++) {
        scanf("%d", &a[i]);
    }

    for (i = 0; i < 5; i++) {
        tmp = a[i];
        a[i] = a[9 - i];
        a[9 - i] = tmp;
    }

    for (i = 0; i < 10; i++) {
        printf("%10d", a[i]);
    }
}