#include <stdio.h>
#include <limits.h>
#include <float.h>

main()
{
    printf("unsigned char max:%ld\n", UCHAR_MAX);
    printf("signed char min:%ld\n", CHAR_MIN);
    printf("signed char max:%ld\n", CHAR_MAX);
    printf("unsigned short max:%ld\n", USHRT_MAX);
    printf("signed short min:%ld\n", SHRT_MIN);
    printf("signed short max:%ld\n", SHRT_MAX);
    printf("unsigned int max:%ld\n", UINT_MAX);
    printf("signed int min:%ld\n", INT_MIN);
    printf("signed int max:%ld\n", INT_MAX);
    printf("unsigned long max:%ld\n", ULONG_MAX);
    printf("signed long min:%ld\n", LONG_MIN);
    printf("signed long max:%ld\n", LONG_MAX);

    printf("float min:%f\n", FLT_MIN);
    printf("float max:%f\n", FLT_MAX);
    printf("double min:%f\n", DBL_MIN);
    printf("double max:%f\n", DBL_MAX);
}