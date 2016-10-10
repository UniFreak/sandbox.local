<?php
/**
 * this iterator does what it says.
 * even if you call rewind() explicitely, it won't rewind
 *
 * use case: you may have an array in whose data can only be used once in this script
 */
include '../../utils.php';
function loop($ary, $time) {
    stringln("---$time loop:---");
    foreach ($ary as $each) {
        stringln($each);
    }
}

$ary = array('one', 'two', 'three', 'four');
$ary = new ArrayIterator($ary);
$ary = new NoRewindIterator($ary);

loop($ary, 'first');
loop($ary, 'second');
$ary->rewind();
loop($ary, 'third');