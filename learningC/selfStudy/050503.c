#include <stdio.h>
#include <string.h>

main()
{
    char res[81], s1[41], s2[41];
    int i, k;
    gets(s1);
    gets(s2);

    i = 0;
    while (s1[i] != '\0') {
        res[i] = s1[i];
        i++;
    }

    k = 0;
    while (s2[k] != '\0') {
        res[i] = s2[k];
        i++;
        k++;
    }
    res[i] = '\0';

    printf("concate result is %s", res);
}