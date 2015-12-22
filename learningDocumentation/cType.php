<?php
require '../helper.func.php';

$testStrs = array(
    'azAZ123',
    "azAZ  \t",
    'jérôme',
    '12345',
    '123.45',
    123.45,
    'az',
    'ZA',
    "\n\r\t",
    );
$testFuncs = array(
    'ctype_alnum', // alphanumeric
    'ctype_alpha', // alphabetic
    'ctype_cntrl', // control characters
    'ctype_digit', // numeric
    'ctype_graph', // printable except space
    'ctype_lower', // lowercase
    'ctype_upper', // uppercase
    'ctype_print', // all printable
    'ctype_punct', // all non-whitespace and non-alphanumeric
    'ctype_space', // whitespace
    'ctype_xdigit',// hexadecimal digit
    );

foreach ($testStrs as $str) {
    echo '====== \'' . addslashes($str) . '\' result:<br />';

    foreach ($testFuncs as $func) {
        echo $func . ':';
        echo ($func($str)) ? 'true<br />' : 'false<br />';
    }
}
