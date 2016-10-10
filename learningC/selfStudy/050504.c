#include <stdio.h>

main()
{
    float s = 0, avg, r = 0;
    int i, n, a[80];

    printf("how many numbers you want to input:");
    scanf("%d", &n);

    for (i = 0; i < n; i++) {
        scanf("%d", &a[i]);
        s += a[i];
    }
    avg = s / n;

    for (i = 0; i < n; i++) {
        r += (a[i] - avg) * (a[i] - avg);
    }
    printf("average is %f, result is %f", avg, r);
}