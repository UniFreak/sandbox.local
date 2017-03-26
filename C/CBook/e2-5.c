#include <stdio.h>
#define YES 1;
#define NO 0;

int any(char s[], char c[]);

main()
{
    char s[] = "hello";
    char c[] = "le";

    printf("le position in hello :%d", any(s, c));
}

/**
 * return position in s[] of any char in c[]
 */
int any(char s[], char c[])
{
    int i, j, k;

    for (i = j = 0; ; i++) {
        for (k = 0; c[k] != '\0'; k++) {
            if (s[i] == c[k]) {
                return i;
            }
        }
    }
    return -1;
}