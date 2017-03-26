#include <stdio.h>

main()
{
    int a, b, max = 0, i;
    scanf("%d", &a);
    do {
        b = a % 10;
        if (b > max) {
            max = b;
        }
        a /= 10;
    } while (a / 10 > 0);
    printf("max is %d", max);
}