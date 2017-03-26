#include <stdio.h>

int bitcount(unsigned x);

main()
{
    int x;
    x = 722;
    printf("there is %d 1 bit in 722", bitcount(722));
}

/* computes word length of the machine */
int bitcount(unsigned x)
{
    int b;

    for (b = 0; x != 0; x >>= 1) {
        if (x & 01) {
            b++;
        }
    }
    return b;
}