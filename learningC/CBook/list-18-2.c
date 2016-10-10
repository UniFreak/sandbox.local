#include <stdio.h>

main()
{
    float nc;

    for (nc = 0; getchar() != EOF; ++nc)
        ;
    printf("total char count: %.0f", nc);
}