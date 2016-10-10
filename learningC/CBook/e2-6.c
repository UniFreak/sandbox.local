#include <stdio.h>

unsigned setbits(unsigned x, int p, int n, unsigned y);

main()
{
    int x, y;
    x = 722;
    y = 25;
    printf("set 722's bit from 4 to 3 as 25's first 3:%d", setbits(x, 4, 3, y));
}

/* set n bits of x at position p with n bits of y */
unsigned setbits(unsigned x, int p, int n, unsigned y)
{
    return (x & ~(07 << (p-n+1))) | ((y & 07) << (p-n+1));
}