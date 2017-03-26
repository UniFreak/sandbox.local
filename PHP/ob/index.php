<?php
/**
 * all browsers seem to show an all-or-nothing approach to buffering.
 * In other words, while php is operating with ob functions, no content can be shown.
 *
 * But if you are not using ob_flush, you run this risk of exceeding socket timeouts
 * (commonly seen in php-fpm/nginx combos).
 * Basically, flushing solves the infamous 504 Gateway Time-out error.
 */

/**
 * How level works
 *
 * Keep in mind that output may be buffered by default, depending on how you are
 * running PHP (CGI, CLI, etc.), or what your `output_buffering` ini setting is.
 *
 * You can use ob_get_level() to determine if an output buffer has already been started.
 * On most web servers, output buffering is already one level deep before my scripts start running
 */
var_dump(
    ob_get_level(),
    ob_get_status($full_status=true),
    ob_list_handlers()
);
ob_implicit_flush(true);
ob_start('ob_gzhandler'); // init level, with built-in gzhandler
echo "Hello ";

ob_start(); // one level deeper
echo "Hello World";
$out2 = ob_get_contents();
ob_end_clean();

echo "Galaxy"; // back to init level
$out1 = ob_get_contents();
ob_end_clean();

var_dump($out1, $out2);

/**
 * function listing
 */
// flush();
// ob_clean();
// ob_end_clean();
// ob_end_flush();
// ob_flush();
// ob_get_clean();
// ob_get_contents();
// ob_get_flush();
// ob_get_length();