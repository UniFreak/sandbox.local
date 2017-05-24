#include <stdio.h>
#include <string.h>

int main(void)
{
    char a[100], *p=a;
    int n1, n2;
    n1 = n2 = 0;
    gets(p);
    while(*p!='\0') {
        if (*p == '(') n1++;
        if (*p == ')') n2++;
        p++;
    }
    if (n1 == n2) printf("od!\n");
}
