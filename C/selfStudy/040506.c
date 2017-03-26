#include <stdio.h>

main()
{
    int i, k;
    int a, b, c;
    for (i = 100; i < 1000; i++) {
        k = i;
        c = k % 10;
        k /= 10;
        b = k % 10;
        k /= 10;
        a = k;

        if (a * a * a + b * b * b + c * c * c == 1099) {
            printf("%d\n", i);
        }
    }
}