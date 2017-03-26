#include <stdio.h>

main()
{
    int a, b[5], i = 0, c;
    scanf("%d", &a);
    do {
        c = a % 10;
        b[i] = c;
        a = a / 10;
        i++;
    } while (a != 0);

    for (--i; i > 0; i--) {
        printf("%d,", b[i]);
    }
    printf("%d", b[i]);
}