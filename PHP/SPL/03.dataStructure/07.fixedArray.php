<?php
/**
 * like array, but with those difference:
 *   - fixed size
 *   - only integer index allowed
 *   - is actually not an array, is an object.
 *     so you can't use it with normal array functions or ArrayIterator
 *   - faster than array, use less memory
 */
include '../../utils.php';

$animals = array('horse', 'aardvark', 'monkey', 'zebra', 'giraffe', 'dog', 'cat');

$animals = SplFixedArray::fromArray($animals);
$animals->offsetUnset(2);
writeln($animals);

$animals = new SplFixedArray(10);
$animals[5] = 'dog';
$animals->setSize(6);
writeln($animals);