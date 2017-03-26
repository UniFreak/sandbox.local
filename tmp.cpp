#include <stdio.h>

int main()
{
    int i, stack[10];
    for (i = 0; i < 10; i++) {
        stack[i] = 1;
    }
    printf("%d", stack[-5]);
}