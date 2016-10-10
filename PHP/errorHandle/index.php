<?php
// #backtrace
function a() {
    b();
}
function b() {
    var_dump(debug_backtrace());
    c();
}
function c() {
    debug_print_backtrace();
}
a();


// #error
function errorHandlerOne($errNo, $errMsg, $errFile, $errLine, $errContext) {
    echo "error handler one: ${errNo}:${errMsg} \n";
}
function errorHandlerTwo($errNo, $errMsg) {
    echo "error handler two: ${errNo}:${errMsg} \n";
}
function errorHandlerThree($errNo, $errMsg) {
    echo "error handler three: ${errNo}:${errMsg} \n";
}

error_reporting(E_ALL);

echo set_error_handler('errorHandlerOne') . "\n";
echo set_error_handler('errorHandlerTwo') . "\n";
echo set_error_handler('errorHandlerThree') . "\n";

trigger_error('user error occur');
restore_error_handler();
user_error('user error occur');
restore_error_handler();
trigger_error('user error occur');
// restore_error_handler();
// trigger_error('user error occur');

print_r(error_get_last());
error_log('logging error' . "\n", 3, './log.err');


// #exception
function exceptionHandlerOne($exception) {
    echo "exception handler one: ".$exception->getMessage()."\n";
}
function exceptionHandlerTwo($exception) {
    echo "exception handler two: ".$exception->getMessage()."\n";
}
function exceptionHandlerThree($exception) {
    echo "exception handler three: ".$exception->getMessage()."\n";
}

echo set_exception_handler('exceptionHandlerOne') . "\n";
echo set_exception_handler('exceptionHandlerTwo') . "\n";
echo set_exception_handler('exceptionHandlerThree') . "\n";

/**
 * NOTE: only triggered handler three, then script end
 */
throw new Exception('new Exception');
restore_exception_handler();
throw new Exception('new Exception');
restore_exception_handler();
throw new Exception('new Exception');
restore_exception_handler();