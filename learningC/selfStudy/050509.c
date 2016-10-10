#include <stdio.h>

main()
{
    char origin[40], decrypted[40], c;
    int i = 0;

    gets(origin);
    while ((c = origin[i]) != '\0') {
        if ((c >= 'A') && (c <= 'Z')) {
            decrypted[i] = c - 3;
        } else if ((c >= 'a') && (c <= 'z')) {
            decrypted[i] = c + 3;
        } else {
            decrypted[i] = c;
        }
        i++;
    }
    decrypted[i] = '\0';
    puts(decrypted);
}