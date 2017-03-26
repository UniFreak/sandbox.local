<?php 
require 'chromePHP/ChromePhp.php';

$cp = chromePHP::getInstance();

$cp->log(__FILE__ . ':' . __LINE__ . ': chromePHP instanciated');
$cp->log('chromePHP:', $cp);

$cp->info('info', 'this is info');
$cp->warn('warn', 'this is warn');
$cp->error('error', 'this is error');

$cp->table('error', 'what');

a('arg', $cp);

/**
 * demo functions
 */
function a($arg, $cp) {
    b($arg, $cp);
}
function b($arg, $cp) {
    c($arg, $cp);
}
function c($arg, $cp) {
    $cp->groupCollapsed('backtrace');
    $cp->log(debug_backtrace());
    $cp->groupEnd();
}