#include <stdio.h>

#define TABINC 8
#define IN 1    /* in space */
#define OUT 0   /* out space */

main()
{
    int c, pos, nb, state, i;

    pos = 1;
    nb = 0;
    state = OUT;
    while ((c = getchar()) != EOF) {
        if ((pos-1) % TABINC == 0 && state == IN && nb > 0) {
            putchar('#');
            nb = 0;
        }

        if (c == ' ') {
            ++nb;
            ++pos;
            state = IN;
        } else {
            if (state == IN && (pos-1) % TABINC != 0) {
                for (i = 0; i < nb; ++i) {
                    putchar(' ');
                }
            }

            nb = 0;
            state = OUT;
            putchar(c);
            ++pos;
        }
    }
}