#include <stdio.h>
#define MAXLINE 1000


int getline(char s[], int lim);
void reverse(char line[]);

main()
{
    char line[MAXLINE];

    while ((getline(line, MAXLINE)) > 0) {
        reverse(line);
        printf("reversed: %s", line);
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

void reverse(char s[])
{
    int i, j;
    char temp;

    i = 0;
    while (s[i] != '\0') { // find the end
        ++i;
    }
    --i; // back off from '\0'

    j = 0;
    while (j < i) { // @note: here is the trick to control process from end to half
        temp = s[j];
        s[j] = s[i];
        s[i] = temp;
        --i;
        ++j;
    }
}