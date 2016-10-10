#include <stdio.h>

unsigned rightrot(unsigned x, int n);
int wordlength(void);

main()
{
    int x;
    x = 722;
    printf("rightrot 722's right 4 bits in %d word length machine:%d", wordlength(), rightrot(x, 4));
}

/* rightrot the n bits of x that begin at postion p*/
unsigned rightrot(unsigned x, int n)
{
    return (x >> n) | (x << (wordlength() - n));
}

/* computes word length of the machine */
int wordlength(void)
{
    int i;
    unsigned v = (unsigned) ~0;

    for (i = 1; (v = v >> 1) > 0; i++)
        ;
    return i;
}