#include <stdio.h>

main()
{
    int c, nl;

    while ((c = getchar()) != EOF) {
        if (c == '\n') {
            ++nl;
        }
    }
    printf("total line: %d", nl);
}