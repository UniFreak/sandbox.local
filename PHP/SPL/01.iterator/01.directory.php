<?php
include '../../utils.php';

// DirectoryIterator extends from SplFileInfo
$dir = new DirectoryIterator('../common/images');
foreach ($dir as $key => $file) {
    // the value $file is an SplFileInfo object
    // checkout splfileinfo.php to see available methods
    stringln($key . ':' . $file->getPathname());
}