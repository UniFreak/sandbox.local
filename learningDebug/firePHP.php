<?php 
require 'firePHP/firePHP.class.php';

$fp = firePHP::getInstance(true);

$fp->log('firePHP instanciated');
$fp->log($fp, 'firePHP instance');

$info = 'this is info';
$fp->info($info, 'info');

$warn = 'this is warn';
$fp->warn($warn, 'warn');

$error = 'this is error';
$fp->error($error, 'error');

a('arg', $fp);

/**
 * demo functions
 */
function a($arg, $fp) {
    b($arg, $fp);
}
function b($arg, $fp) {
    c($arg, $fp);
}
function c($arg, $fp) {
    $fp->trace('Trace');
}