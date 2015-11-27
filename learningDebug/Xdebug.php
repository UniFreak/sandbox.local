<?php 

// var_dump() output is beautified

// xdebug_debug_zval() and
// xdebug_get_declared_vars()
//      for this to work, must set `collect_vars`&`show_local_vars`&`collect_params`
function a($arg) {
    xdebug_debug_zval('arg');
    b($arg);
}
function b(&$arg) {
    xdebug_debug_zval('arg');
    c($arg);
}
function c($arg) {
    xdebug_debug_zval('arg');
    $declared = 'declared';
    var_dump(xdebug_get_declared_vars());
}
a('arg');

// for remote debugging, search the web


// profiling(@failed)
/**
 * slow function
 */
function slow() {
    for ($i = 0; $i <= 10000; $i++) {
        // do nothing...
    }
}
/**
 * slower function
 */
function slower() {
    usleep(50000);
}
for ($i = 0; $i < 50; $i++) {
    slow();
}
slower();