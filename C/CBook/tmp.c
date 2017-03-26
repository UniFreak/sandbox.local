#include <stdio.h>
char const *f(void) { return "Hello, world!"; }

int
main(void)
{
  // char const *f(void);
  printf("%s\n", f());
}
