<?php
/**
 * link is like array, but their node know their neighbors
 * use it if you want to process it in sequence in either direction
 * it's not designed for random access, but you do can remove/add an element
 * from/into the middle of the list
 *
 * in this script, we are trying to reorder the course by author's last name
 *
 * available methods:
 *   - add
 *   - bottom
 *   - count
 *   - current
 *   - getIteratorMode
 *   - isEmpty
 *   - key
 *   - next
 *   - offsetExists
 *   - offsetGet
 *   - offsetSet
 *   - offsetUnset
 *   - pop
 *   - prev
 *   - push
 *   - rewind
 *   - serialize
 *   - setIteratorMode
 *   - shift
 *   - top
 *   - unserialize
 *   - unshift
 *   - valid
 */
include '../../utils.php';
function loopThis($courses, $order) {
    stringln($order);
    foreach ($courses as $course) {
        stringln($course->author . ':' . $course->title);
    }
}

$data = simplexml_load_file('../common/data/courses.xml');
$courses = new SplDoublyLinkedList();

function getLastName($author) {
    $pos = strrpos($author, ' ');
    return substr($author, $pos+1);
}

foreach ($data as $item) {
    if ($courses->isEmpty()) {
        $courses->push($item);
    } else {
        $lastName = $item->author;
        $courses->rewind();
        // iterate to the right insert position
        while ($courses->valid() &&
            getLastName($courses->current()->author) <= $lastName) {
            $courses->next();
        }
        // and do the insertion
        $courses->add($courses->key(), $item);
    }
}

loopThis($courses, '---normal order---');

$courses->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO);
loopThis($courses, '---reversed order---');