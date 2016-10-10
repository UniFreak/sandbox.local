<?php
/**
 * even though they both can be foreached, array is not iterator, and
 * ArrayIterator is not array.
 *
 * use the following technique to convert bettwen them
 *
 * ArrayIterator has many methods resemble array related functions, RTFM
 */
include '../../utils.php';
function loop($langs) {
    foreach ($langs as $lang) {
        stringln($lang);
    }
    stringln('------');
}

$langsAry = array('Javascript', 'C', 'PHP', 'C++', 'Python', 'Java');

// array to ArrayIterator
$langsItr = new ArrayIterator($langsAry);
$langsItr = new LimitIterator($langsItr, 2, 3);
loop($langsItr);

// ArrayIterator to array(the whole)
$langsAryWhole = $langsItr->getArrayCopy();
loop($langsAryWhole);

// ArrayIterator to array(limited)
$langsAryLmt = iterator_to_array($langsItr);
loop($langsAryLmt);
