#include <stdio.h>

main()
{
    float s[5][3], sum;
    int i, k;

    for (i = 0; i < 5; i++) {
        for (k = 0; k < 3; k++) {
            scanf("%f", &s[i][k]);
        }
    }

    for (i = 0; i < 5; i++) {
        sum = 0;
        for (k = 0; k < 3; k++) {
            sum += s[i][k];
        }
        printf("student %d's average score is %f\n", i+1, sum/3);
    }
}