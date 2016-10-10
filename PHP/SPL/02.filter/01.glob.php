<?php
/**
 * glob pattern(not regex!):
 *  - ?
 *  - *
 *  - [abc]
 *  - [3-7]
 *  - [!abc]
 */
include '../../utils.php';

// use absolute path here to be more portable, becuase on windows, GlobIterator
// only work if passed in a absolute path
$files = new GlobIterator(__DIR__ . '/common/images/*.jpg');
foreach ($files as $file) {
    // GlobIterator extends from FilesystemIterator, so $file
    // will be a SplFileInfo object
    stringln($file->getFilename());
}