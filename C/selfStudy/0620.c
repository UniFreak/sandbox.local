#include <stdio.h>

int main() {
    int a[5], *pa[5], *max;
    int i;
    for (i = 0; i < 5; i++) {
        pa[i] = &a[i];
    }
    for (i = 0; i < 5; i++) {
        scanf("%c", pa[i]);
    }
    max = pa[0];
    for (i = 1; i < 5; i++) {
        if (*pa[i] > *max) {
            max = pa[i];
        }
    }
    printf("%c\n", *max);
}