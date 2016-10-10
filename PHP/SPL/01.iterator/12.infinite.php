<?php
/**
 * the use of InfiniteIterator is to handle repetitive consequence without the
 * fuss of manully rewind. chain into LimitIterator to avoid infinite loop
 */
include '../../utils.php';

$days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturady');
$days = new ArrayIterator($days);
// note: infiniteIterator first, then Limit
$days = new InfiniteIterator($days);
$days = new LimitIterator($days, 3, 7);
foreach ($days as $day) {
    stringln($day);
}