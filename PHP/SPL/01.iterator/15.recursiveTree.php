<?php
/**
 * RecursiveTreeIterator helps you to generate a ASCII graphic tree
 * you can also customize how the tree look, by setting several predefiend
 * constants, like this:
 *
 * $files->setPrefixPart(RecursiveTreeIterator::PREFIX_MID_HAS_NEXT, '|');
 *
 * available constants are:
 *   - PREFIX_LEFT
 *   - PREFIX_MID_HAS_NEXT
 *   - PREFIX_MID_LAST
 *   - PREFIX_END_HAS_NEXT
 *   - PREFIX_END_LAST
 *   - PREFIX_RIGHT
 * RTFM
 */
include '../../utils.php';

$files = new RecursiveDirectoryIterator('../common/');
$files->setFlags(RecursiveDirectoryIterator::SKIP_DOTS |
    RecursiveDirectoryIterator::UNIX_PATHS);
$files = new RecursiveTreeIterator($files);
foreach ($files as $file) {
    stringln($file);
}