#include <stdio.h>

main()
{
    int c, sl;

    while ((c = getchar()) != EOF) {
        if (c == ' ' || c == '\t' || c == '\n') {
            ++sl;
        }
    }
    printf("white space count: %d", sl);
}