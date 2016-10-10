#include <stdio.h>

int f2c(int fhar);

main()
{
    int fahr, celsius;
    int lower, upper, step;

    lower = 0;
    upper = 300;
    step = 20;

    fahr = lower;
    while (fahr <= upper) {
        printf("%d\t%d\n", fahr, f2c(fahr));
        fahr = fahr + step;
    }
}

int f2c(int fahr) {
    return 5 * (fahr-32) / 9;
}