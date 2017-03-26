#include <stdio.h>

int atoi(char s[]);
int lower(int c);

main()
{
    printf("123ab:%d\n", atoi("123ab"));
    printf("ab123:%d\n", atoi("ab123"));
    printf("c:%s\n", lower('c'));
    printf("C:%s\n", lower('C'));
}

int atoi(char s[])
{
    int i, n;

    n = 0;
    for (i = 0; s[i] >= '0' && s[i] <= '9'; ++i) {
        n = 10 * n + (s[i] - '0');
    }
    return n;
}

int lower(int c)
{
    if (c >= 'A' && c <= 'Z') {
        return c + 'a' - 'A';
    } else {
        return c;
    }
}