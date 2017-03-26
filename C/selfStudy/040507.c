#include <stdio.h>

main()
{
    int cp = 0, cn = 0, cz = 0;
    int i, a;
    for (i = 0; i < 10; i++) {
        scanf("%d", &a);
        if (a > 0) {
            cp++;
        } else if (a == 0) {
            cz++;
        } else {
            cn++;
        }
    }

    printf("positive count: %d, negative count: %d, zero count: %d", cp, cn, cz);
}