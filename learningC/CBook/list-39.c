#include <stdio.h>

int strlen(char s[]);

main()
{
    printf("'test' length:%d", strlen("test"));
}

int strlen(char s[])
{
    int i;

    i = 0;
    while (s[i] != '\0') {
        ++i;
    }
    return i;
}