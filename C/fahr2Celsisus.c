#include <stdio.h>

#define LOWER 0
#define UPPER 300
#define STEP  20

main()
{
    float fahr, celsius;

    printf("fahr to celsius conversion table\n");
    fahr = UPPER;
    while (fahr >= LOWER) {
        celsius = (5.0/9.0) * (fahr-32);
        printf("%3.0f %6.1f\n", fahr, celsius);
        fahr = fahr - STEP;
    }
}