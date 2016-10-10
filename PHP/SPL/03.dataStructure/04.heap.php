<?php
/**
 * extends from SplHeap to implement complex sorting
 */
include '../../utils.php';

class SortCourses extends SplHeap
{
    /**
     * - if both are equeal, return 0
     * - if the first is greater than the second
     *     - maxheap: return a positive number
     *     - minheap: return a negative number
     * - if the second is greater than the first
     *     - maxheap: return a negative number
     *     - minheap: return a positive number
     *
     * here we are building a minheap
     */
    public function compare($val1, $val2) {
        $val1 = $val1->title;
        $val2 = $val2->title;
        if ($val1 == $val2) {
            return 0;
        } elseif ($val1 > $val2) {
            return -1;
        } else {
            return 1;
        }
    }
}

$data = file_get_contents('../common/data/courses.json');
$data = json_decode($data);
$courses = new SortCourses();
foreach ($data as $course) {
    $courses->insert($course);
}
foreach ($courses as $course) {
    stringln("$course->title with $course->author");
}