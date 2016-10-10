<?php
function tick() {
    echo 'tick' . "\n";
}
declare(ticks = 1);
register_tick_function('tick');
echo ''; echo '';
unregister_tick_function('tick');


function sum() {
    echo 'total ' . func_num_args() . ' passed in:' .
        'first:' . func_get_arg(0) . ';' .
        'all:' . implode(',', func_get_args()) . "\n";
}
sum('one', 'two', 3);
echo function_exists('sum') . "\n";

function foobar($arg, $arg2) {
    echo __FUNCTION__, " got $arg and $arg2\n";
}
class foo {
    function bar($arg, $arg2) {
        echo __METHOD__, " got $arg and $arg2\n";
    }
    static function staticBar($arg, $arg2) {
        echo __METHOD__, " got $arg and $arg2\n";
    }
}

call_user_func_array("foobar", array("one", "two"));
call_user_func("foobar", 'one', 'two');

$foo = new foo;
call_user_func_array(array($foo, "bar"), array("three", "four"));
call_user_func(array($foo, "bar"), "three", "four");

call_user_func_array('foo::staticBar', array("five", "six"));
call_user_func('foo::staticBar', "five", "six");


function shutdown() {
    echo 'shutting down...' . "\n";
}
register_shutdown_function('shutdown');


// print_r(get_defined_functions());