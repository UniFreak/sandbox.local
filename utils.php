<?php
function writeln($var) {
    if (php_sapi_name() == 'cli') {     // in command line
        print_r($var);
        echo "\n";
    } else {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
        echo '<br />';
    }
}