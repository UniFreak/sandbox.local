#include <stdio.h>

main()
{
    double r = 1, y = 0, e;

    while ((e = 1 / (r*r + 1)) >= 1E-6) {
        y += e;
        r++;
    }

    printf("y is %lf", y);
}