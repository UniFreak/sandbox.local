<?php
include '../../utils.php';

$files = new RecursiveDirectoryIterator('../../');
$files = new RecursiveIteratorIterator($files);
// also there is a RecursiveRegexIterator, but it's more difficult
// to get work. so better stick with this(RecursiveDirectory+Regex)
$files = new RegexIterator($files, '/\.(?:jpg|docx)/i');
foreach ($files as $file) {
    stringln($file->getFilename());
}