#include <stdio.h>

#define OUT 1
#define IN 0

main()
{
    int c, nl, nw, nc, state;

    state = OUT;
    nl = nw = nc = 0;
    while ((c = getchar()) != EOF) {
        ++nc;

        if (c == '\n') {
            ++nl;
        }

        if (c == ' ' || c == '\t' || c == '\n') {
            state = OUT;
        } else if (state == OUT) {
            state = IN;
            ++nw;
        }
    }
    printf("char count:%d, word count:%d, line count:%d", nc, nw, nl);
}