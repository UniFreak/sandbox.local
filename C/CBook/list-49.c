#include <stdio.h>

unsigned getbits(unsigned x, int p, int n);

main()
{
    printf("get 5 bits from position 6 of 1234:%d", getbits(1234, 6, 5));
}

/* get n bits from position p*/
unsigned getbits(unsigned x, int p, int n)
{
    return (x >> (p+1-n)) & ~(~0 << n);
}