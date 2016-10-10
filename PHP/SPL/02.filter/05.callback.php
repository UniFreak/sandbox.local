<?php
include '../../utils.php';

function getBeginner($current) {
    return strtolower($current->level) == 'intermediate';
}

$courses = simplexml_load_file('../common/data/courses.xml', 'SimpleXMLIterator');
$courses = new CallbackFilterIterator($courses, 'getBeginner');
foreach ($courses as $course) {
    stringln("$course->title (level:$course->level)");
}