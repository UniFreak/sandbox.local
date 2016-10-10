#include <stdio.h>
#define MAXLINE 1000

int getline(char line[], int maxline);

main()
{
    char line[MAXLINE];
    int i, len;

    while ((len = getline(line, MAXLINE)) > 0) {
        i = len-2;
        while ((line[i] == ' ' || line[i] == '\t') && i >= 0) {
            line[i] = '*';
            --i;
        }
        if (i >= 0) {
            printf("%s", line);
        }

    }
    return 0;
}

int getline(char s[], int lim)
{
    int c, i;

    for (i = 0; i < lim-1 && (c=getchar()) != EOF && c != '\n'; ++i) {
        s[i] = c;
    }
    if (c == '\n') {
        s[i] = c;
        ++i;
    }
    s[i] = '\0';
    return i;
}