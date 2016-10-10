<?php
echo 'a', 'b', 'c', "\n"; // NOTE: can echo multiple divided by ','

echo addcslashes("fOo[ ]\n", 'A..Z'); // custom chars that need to add slash
echo stripcslashes("f\Oo[ ]\n");

/**
 * use case: when inserting data into database
 *   but better option is DBMS specific functions like
 *   `mysqli_real_escape_string()` or `pg_escape_string()`
 * NOTE: if magic_quotes_gpc is on, all GET, POST, COOKIE are aut slashed
 */
echo addslashes("fOo[ ]'\\n\n"); // ', ", \, NUL are added
echo stripslashes("fOo[ ]\'\\n\n");

echo strcasecmp('Hello', 'HELLO') . "\n"; // case-insensitive comparison
echo strncasecmp('Hello', 'HELLO', 2) . "\n"; // case-insensitive first n letters comparison
echo strcmp('Hello', 'HELLO') . "\n"; // case-sensitive comparison
echo strncmp('Hello', 'HELLO', 1) . "\n"; // case-sensitive first n letters comparison
echo substr_compare('HellYeah', 'Helloeah', -2, 1, false) . "\n"; // father of above three

echo strnatcasecmp('A1', 'a02') . "\n";
echo strnatcmp('A1', 'a02') . "\n";
