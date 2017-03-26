#include <stdio.h>

#define OUT 1
#define IN 0

main()
{
    int c, state;

    state = OUT;
    while ((c = getchar()) != EOF) {
        if (c == ' ' || c == '\t' || c == '\n') {
            if (state == IN) {
                putchar('\n');
            }
            state = OUT;
        } else {
            putchar(c);
            state = IN;
        }
    }
}