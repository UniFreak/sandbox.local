#include <stdio.h>

main()
{
    float c, f;
    scanf("%f", &f);
    c = 5 * (f - 32) / 9;
    printf("c is %f", c);
}