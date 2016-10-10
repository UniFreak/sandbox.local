#include <stdio.h>

main()
{
    /* getchar returns a distinctive value when there is no more input, a value that cannot be confused with any real character. This value is called  EOF , for "end of file"s. We must declare  c to be a type big enough to hold any value that  getchar returns. We can't use  char since  c must be big enough to hold  EOF in addition to any possible  char . Therefore we use  int */
    int c;

    c = getchar();
    while (c != EOF) {
        putchar(c);
        c = getchar();
    }
}