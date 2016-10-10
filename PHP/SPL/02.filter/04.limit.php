<?php
include '../../utils.php';

$courses = simplexml_load_file('../common/data/courses.xml', 'SimpleXMLIterator');
$courses = new LimitIterator($courses, 20, 10); // from 20 to 30

foreach ($courses as $course) {
    stringln($courses->getPosition()+1 . ". $course->title with $course->author");
}