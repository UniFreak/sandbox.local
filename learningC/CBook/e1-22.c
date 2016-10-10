#include <stdio.h>
#define LINEMAX 10

void dumpTill(int here, char target[]);

int c, pos, lastBlank;
char buffer[LINEMAX];

main()
{
    pos = 0;
    while ((c=getchar()) != EOF) {
        if (c == '\n') {
            dumpTill(LINEMAX - 1, buffer);
        } else {
            if (pos == LINEMAX) {
                if (c == '\t' || c == ' ' || lastBlank == 0) {
                    dumpTill(LINEMAX - 1, buffer);
                } else {
                    dumpTill(lastBlank, buffer);
                    lastBlank = 0;
                }
                pos = 0;
            } else {
                buffer[pos] = c;
                if (c == '\t' || c == ' ') {
                    lastBlank = pos;
                }
                ++pos;
            }
        }
    }
}

void dumpTill(int here, char target[]) {
    for (i = 0; i <= here; i++) {
        putchar(target[i]);
    }
    putchar('\n');
}