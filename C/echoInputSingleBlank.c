#include <stdio.h>

main()
{
    int c, putNext;
    int isBlank;

    putNext = 1;
    while ((c = getchar()) != EOF) {
        isBlank = (c == ' ' || c == '\t' || c== '\n');
        if (isBlank) {
            while ((c = getchar()) == ' ') {
                // do nothing
            }
            putchar (' ');
            if (c == EOF) break;
        }
        putchar(c);
    }
}