#include <stdio.h>

main()
{
    float x, y, z;
    scanf("%f, %f, %f", &x, &y, &z);
    if (x < y && x < z) {
        printf("%f", x);
    } else if (y < x && y < z) {
        printf("%f", y);
    } else if (z < x && z < y) {
        printf("%f", z);
    } else {
        printf("no min value");
    }
}