#include <stdio.h>
main() {
    int n = 5;
    int *p;
    p = &n;
    printf("addres of n: %x\n", &n);
    printf("value of p: %x\n", p);
    printf("value of n: %x\n", *p);
    printf("size of int: %x\n", sizeof(int));
}