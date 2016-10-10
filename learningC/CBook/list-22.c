#include <stdio.h>

main()
{
    int c, i, nwhite, nother;
    int ndigit[10];

    nwhite = nother = 0;
    for (i = 0; i < 10; i++) {
        ndigit[i] = 0;
    }

    while ((c = getchar()) != EOF) {
        if (c >= '0' && c <= '9') {
            ++ndigit[c - '0'];
        }

        if (c == ' ' || c == '\t' || c == '\n') {
            ++nwhite;
        } else {
            ++nother;
        }
    }
    printf("digit count = ");
    for (i = 0; i < 10; i++) {
        printf("%d", ndigit[i]);
    }
    printf(", white count = %d, other count = %d", nwhite, nother);
}