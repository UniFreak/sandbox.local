#include <stdio.h>

main()
{
    int i;
    for (i = 0x30; i <= 0x5f; i++) {
        printf("integer is %d and char is %c\n", i, i);
    }
}