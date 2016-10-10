#include <stdio.h>

main()
{
    float a[10], sum = 0, avg;
    int i;

    for (i = 0; i < 10; i++) {
        scanf("%f", &a[i]);
    }

    for (i = 0; i < 10; i++) {
        sum += a[i];
    }
    avg = sum / 10;

    printf("average is %f", avg);
}