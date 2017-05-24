#include <stdio.h>

int main()
{
    float a[20], x, *pb, *pe;
    int n, i;
    scanf("%d", &n);
    pb = a;
    for (i = 0; i < n; i++) {
        scanf("%f", pb++);
    }
    for (i=1, pb=a, pe=a+n-1; i <= n/2; i++,pb++,pe--) {
        x = *pb, *pb = *pe, *pe = x;
    }
    for (pb=a; pb < a+n; pb++) {
        printf("%f ", *pb);
    }
}