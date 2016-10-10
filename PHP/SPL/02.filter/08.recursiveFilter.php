<?php
include '../../utils.php';

class FilterByExtensions extends RecursiveFilterIterator
{
    protected $extensions;

    /**
     * to override a RecursiveIteratorIterator's constructor, you need also
     * override the getChildren() method below
     */
    public function __construct(RecursiveIterator $iterator, $extensions)
    {
        parent::__construct($iterator);
        $this->extensions = (array) $extensions;
    }

    public function getChildren()
    {
        return new self($this->getInnerIterator()->getChildren(), $this->extensions);
    }

    public function accept()
    {
        if ($this->hasChildren()) {
            return true;
        }
        return $this->current()->isFile() &&
            in_array(strtolower($this->current()->getExtension()), $this->extensions);
    }
}

$files = new RecursiveDirectoryIterator('../');
$files->setFlags(FilesystemIterator::UNIX_PATHS);
$files = new FilterByExtensions($files, array('csv', 'txt', 'jpg'));
$files = new RecursiveIteratorIterator($files);
foreach ($files as $file) {
    stringln($file->getPathname());
}