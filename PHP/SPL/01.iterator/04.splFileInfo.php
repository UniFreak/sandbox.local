<?php
include '../../utils.php';

$files = new FilesystemIterator('../common/images', FilesystemIterator::UNIX_PATHS);
foreach ($files as $file) { // $file is a SplFileInfo object
    stringln('filename: ' . $file->getFilename());
    stringln('basename: ' . $file->getBasename());
    stringln('relative path(without trailing slash): ' . $file->getPathname());
    stringln('relative path(without trailing slash or filename): ' . $file->getPath());
    stringln('absolute path: ' . $file->getRealPath());

    stringln('filegroup: ' . $file->getGroup());
    stringln('owner id: ' . $file->getOwner());
    stringln('permissions: ' . $file->getPerms());
    stringln('inode number: ' . $file->getInode());
    stringln('link target: ' . $file->getLinkTarget());

    stringln('size: ' . $file->getSize() . ' bytes');
    stringln('extension: ' . $file->getExtension());
    stringln('type: ' . $file->getType());
    stringln('last accessed time: ' . $file->getATime());
    stringln('last modified time: ' . $file->getMTime());
    stringln('last changed time: ' . $file->getCTime());

    stringln('is a file: ' . $file->isFile());
    stringln('is a dir: ' .  $file->isDir());
    stringln('is a link: ' .  $file->isLink());
    stringln('is readable: ' . $file->isReadable());
    stringln('is writable: ' . $file->isWritable());
    stringln('is Executable: ' . $file->isExecutable());

    stringln('SplFileObject:(to access its content):' . $file->openFile());
    stringln('parent\'s SplFileInfo object: ' . $file->getPathInfo());
    stringln('self\'s SplFileInfo : ' . $file->getFileInfo());

    stringln('setting class returned when calling openFile:' . $file->setFileClass());
    stringln(
        'setting class returned when calling getFileInfo/getPathInfo:' .
        $file->setInfoClass()
    );

    stringln('');
}