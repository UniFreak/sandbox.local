<?php
include '../../utils.php';

// FilesystemIterator extends from DirectoryIterator
$files = new FilesystemIterator('../common/images');
$files->setFlags(
    FilesystemIterator::UNIX_PATHS |
    FilesystemIterator::KEY_AS_FILENAME // use filename as key
    );
foreach ($files as $key => $file) {
    stringln($key . ':' . $file->getPathname());
}