<?php
/**
 * Heap:
 *   - binary tree structur
 *   - each node has no more than two children
 *   - root node contains the lowest(min heap) or highest(max heap) value
 * Characters:
 *   - items are not stored in a predictable sequence
 *   - access sorted items by removing root node until empty(this destory the heap)
 *   - very efficient way of sorting items when they're added
 * Usage:
 *   - sorting lists that are used only once
 *   - for simple sorting(by alpha or numeric), see heap.php for complex sorting
 *   - not suitable for lists that are traversed repeatedly(that for doublyLinkedList)
 *
 * Methods:
 *   - top()
 *   - next() // this method may not be as your expected, RTFM
 *   - current()
 */
include '../../utils.php';

$animals = array('horse', 'aardvark', 'monkey', 'zebra', 'giraffe', 'dog', 'cat');

$min = new SplMinHeap();
foreach ($animals as $animal) {
    $min->insert($animal);
}

$max = new SplMaxHeap();
foreach ($animals as $animal) {
    $max->insert($animal); // cannot use [] like array to add items
}

writeln($min);
writeln($max);

loop($min, 'loop and destory min');
loop($max, 'loop and destory max');

// iteration will destory them, so now they are empty
writeln($min);
writeln($max);
