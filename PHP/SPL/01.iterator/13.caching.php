<?php
/**
 * I don't know what's the purpose of CachingIterator, here it just seems to use
 * the hasNext() method to check whether a element is the last
 */
include '../../utils.php';

$ary = array(1, 3, 5, 7, 9, 'last');
$ary = new ArrayIterator($ary);
$ary = new CachingIterator($ary);
foreach ($ary as $node) {
    if ($ary->hasNext()) {
        stringln($node);
    }
}