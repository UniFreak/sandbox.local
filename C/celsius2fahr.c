#include <stdio.h>

#define LOWER 0
#define UPPER 300
#define STEP  20

main()
{
    float celsius, fahr;

    printf("celsius to fahr conversion table\n");
    celsius = LOWER;
    while (celsius <= UPPER) {
        fahr = (9.0/5.0) * celsius + 32;
        printf("%3.0f %6.1f\n", celsius, fahr);
        celsius = celsius + STEP;
    }
}