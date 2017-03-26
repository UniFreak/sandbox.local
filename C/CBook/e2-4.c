#include <stdio.h>
#define YES 1;
#define NO 0;

void squeeze(char s[], char c[]);

main()
{
    char s[] = "hello";
    char c[] = "le";

    squeeze(s, c);
    printf("hello with out le:%s", s);
}

/**
 * delete all chars in c[] from s[]
 */
void squeeze(char s[], char c[])
{
    int i, j, k, match;

    for (i = j = 0; s[i] != '\0'; i++) {
        for (k = 0; c[k] != '\0'; k++) {
            match = NO;
            if (s[i] == c[k]) {
                match = YES;
                break;
            }
        }
        if (!match) {
            s[j++] = s[i];
        }
    }
    s[j] = '\0';
}