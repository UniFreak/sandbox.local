#include <stdio.h>

main()
{
    char origin[40], encrypted[40], c;
    int i = 0;

    gets(origin);
    while ((c = origin[i]) != '\0') {
        if ((c >= 'A') && (c <= 'Z')) {
            encrypted[i] = c + 3;
        } else if ((c >= 'a') && (c <= 'z')) {
            encrypted[i] = c - 3;
        } else {
            encrypted[i] = c;
        }
        i++;
    }
    encrypted[i] = '\0';
    puts(encrypted);
}