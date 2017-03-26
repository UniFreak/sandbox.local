#include <stdio.h>
#include <string.h>

int main()
{
    int c;
    int state=1; // 0 you didn't print newline 1 you printed newline
    while((c = getchar()) != EOF)
    {
        // if the character read is a blank or newline or tab
        if (c == ' ' || c == '\n' || c == '\t' )
        {
            //check the status to see if we printed a newline before
            if(state==0)
            putchar('\n');
            state=1;
        }
        else
        {
            //printf("la valeur est %d  %c\n",c);
            putchar(c);
            //turn the status off in order to print newline in the next
            // blank newline or tab character
            state=0;
        }
    }

    return 0;
}