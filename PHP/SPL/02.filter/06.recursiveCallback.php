<?php
include '../../utils.php';

$files = new RecursiveDirectoryIterator('../../');
$files->setFlags(RecursiveDirectoryIterator::SKIP_DOTS);
$files = new RecursiveCallbackFilterIterator(
    $files,
    function($current, $key, $iterator) {
        if ($iterator->hasChildren()) {
            return true; // so it can iterate inside and continue
        }
        return $current->getSize() > 1024*6; // >6KB
    });
// chaining like crazy, but this is a must
// see recursiveDirectory.php
$files = new RecursiveIteratorIterator($files);

foreach ($files as $file) {
    stringln($file->getPathname() . " is " .
        round($file->getSize() / 1024, 1) .
        'KB large');
}