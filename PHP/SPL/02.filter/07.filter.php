<?php
include '../../utils.php';
function loop($courses) {
    foreach ($courses as $course) {
        stringln("$course->title with author $course->author");
    }
    stringln('-----');
}

/**
 * FilterIterator is a abstract class
 * so you have to implement it
 */
class AuthorFilter extends FilterIterator
{
    protected $author;

    public function __construct(Iterator $iterator, $author)
    {
        parent::__construct($iterator);
        $this->author = $author;
    }

    // the only must
    public function accept()
    {
        return $this->current()->author == $this->author;
    }
}

// using xml
$courses = simplexml_load_file('../common/data/courses.xml', 'SimpleXMLIterator');
$courses = new AuthorFilter($courses, 'David Powers');
loop($courses);

// using json, see array.php
$courses = file_get_contents('../common/data/courses.json');
$courses = json_decode($courses);
$courses = new ArrayIterator($courses);
$courses = new AuthorFilter($courses, 'Kevin Skoglund');
loop($courses);