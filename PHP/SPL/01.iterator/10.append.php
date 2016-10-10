<?php
/**
 * append iterator let you append several different iterator into one iterator
 * so they can run succesively
 */

include '../../utils.php';
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

$courses = simplexml_load_file('../common/data/courses.xml', 'SimpleXMLIterator');
$power = new AuthorFilter($courses, 'David Powers');
$peck = new AuthorFilter($courses, 'Jon Peck');
$courses = new AppendIterator();
$courses->append($peck);
$courses->append($power);

$previous = '';
foreach($courses as $course) {
    if ($previous != $course->author) {
        stringln("Courses by $course->author:");
    }
    stringln("  $course->title");
    $previous = (string) $course->author; // note the (string)
}