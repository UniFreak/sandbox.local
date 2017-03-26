#include <stdio.h>

unsigned invert(unsigned x, int p, int n);

main()
{
    int x;
    x = 722;
    printf("invert 722's 3 bit from position 4:%d", invert(x, 4, 3));
}

/* invert the n bits of x that begin at postion p*/
unsigned invert(unsigned x, int p, int n)
{
    return x ^ (~(~0 << n) << (p+1-n));
}