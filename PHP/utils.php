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

function stringln($var) {
    if (php_sapi_name() == 'cli') {     // in command line
        echo $var . "\n";
    } else {
        echo $var . '<br />';
    }
}

function loop($iterable, $header) {
    stringln("---$header:---");
    foreach ($iterable as $each) {
        stringln($each);
    }
}