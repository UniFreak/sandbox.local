<?php
include '../../utils.php';

// RecursiveDirectoryIterator extends from FilesystemIterator
$files = new RecursiveDirectoryIterator('../common');
// so you can use any flags in FilesystemIterator
$files->setFlags(
    FilesystemIterator::SKIP_DOTS |
    FilesystemIterator::UNIX_PATHS
);
// chain into RecusiveIteratorIterator to auto tranverse into sub directory. @note: this is a must in order to auto tranverse work
$files = new RecursiveIteratorIterator(
    $files,
    RecursiveIteratorIterator::SELF_FIRST // by default, RecursiveIteratorIterator
                                          // only access child, pass this flag to
                                          // display directory name
);
$files->setMaxDepth(0); // control how deep it goes

foreach ($files as $file) {
    stringln($file);
}