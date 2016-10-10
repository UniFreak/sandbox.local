<?php
include '../../utils.php';

// the second parameter associate $courses with SimpleXMLIterator, so now $courses
// is a iterator, so that we can pass it into RegexIterator's constructor
$courses = simplexml_load_file('../common/data/courses.xml', 'SimpleXMLIterator');
foreach ($courses as $course) {
    // @note: instantiate RegexIterator inside the loop
    $matchs = new RegexIterator($course->author, '/joh?n peck/i');
    foreach ($matchs as $match) {
        stringln($course->title . ' with ' . $course->author);
    }
}