#include <stdio.h>

#define MAXCHAR 26

main()
{
    int c, i, j;
    int lc[MAXCHAR];

    for (i = 0; i < MAXCHAR; i++) {
        lc[i] = 0;
    }

    while ((c = getchar()) != EOF) {
        i = c - 'a';
        if (i >= 0 && i < MAXCHAR) {
            ++lc[i];
        }
    }

    for (i = 0; i < MAXCHAR; i++) {
        printf("%2d:", i);
        for (j = 0; j < lc[i]; j++) {
            putchar('*');
        }
        putchar('\n');
    }
}