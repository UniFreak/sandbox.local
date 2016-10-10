#include <stdio.h>

main()
{
    int i, o;
    scanf("%d", &i);
    if (i >= 0) {
        o = 1;
    } else {
        o = -1;
    }
    printf("%d", o);
}