#include <stdio.h>

void squeeze(char s[], int c);

main()
{
    char s[] = "hello";
    char c = 'l';

    squeeze(s, c);
    printf("hello with outh l:%s", s);
}

/**
 * delete all c from s
 */
void squeeze(char s[], int c)
{
    int i,j;

    for (i = j = 0; s[i] != '\0'; i++) {
        if (s[i] != c) {
            s[j++] = s[i];
        }
    }
    s[j] = '\0';
}