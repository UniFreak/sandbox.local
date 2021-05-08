# `php://`

- `php://stdin, php//stdout, php//stderr`

if you open php://stdin and later close it, you close only your copy of the descriptor-the actual stream referenced by STDIN is unaffected. It is recommended that you simply use the constants STDIN, STDOUT and STDERR instead of manually opening streams using these wrappers

- `php://input`

it is preferable to use php://input instead of $HTTP_RAW_POST_DATA as it does not depend on special php.ini directives. php://input is not available with enctype="multipart/form-data".

- `php://output`

- `php://fd`

- `php://memory, php://temp`

- `php://filter`

