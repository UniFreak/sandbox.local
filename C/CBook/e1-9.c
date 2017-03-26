#include <stdio.h>

main()
{
    int c, inBlank;

    while ((c = getchar()) != EOF) {
        if (c != ' ' && c != '\t' && c != '\n') {
            putchar(c);
            inBlank = 0;
        } else if (!inBlank) {
            putchar(c);
            inBlank = 1;
        }
    }
}