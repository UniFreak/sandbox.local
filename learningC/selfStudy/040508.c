#include <stdio.h>

main()
{
    double e = 1.0, i;
    int n = 1;
    do {
        i = 1 / ((1 + n) * n / 2);
        e += i;
        n++;
    } while (i >= 1E-6);

    printf("e is %lf", e);
}