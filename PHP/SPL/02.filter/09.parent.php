<?php
/**
 * parent filter used to find elements that have children
 * useful if you're trying to build a menu or list top level categories
 */
include '../../utils.php';

$dirs = new RecursiveDirectoryIterator('../');
$dirs = new ParentIterator($dirs);
$dirs = new RecursiveIteratorIterator(
    $dirs,
    RecursiveIteratorIterator::SELF_FIRST // don't forget this when using
                                          // ParentFilter!!
                                          // else the default mode(LEAVES_ONLY)
                                          // will ignore directory and generate
                                          // blank result
    );

foreach ($dirs as $dir) {
    stringln($dir->getFilename());
}