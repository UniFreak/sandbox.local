<?php
include '../../utils.php';

$docs = new FilesystemIterator('../common/documents', FilesystemIterator::UNIX_PATHS);
foreach ($docs as $file) {
    if ($file->getExtension() == 'txt') {
        $textFile = $file->openFile('r+'); // return a SplFileObject, in r+ mode
        $textFile->setFlags(
            SplFileObject::SKIP_EMPTY |
            SplFileObject::READ_AHEAD |
            SplFileObject::DROP_NEW_LINE);

        // becuase SplFileObject extends from SplFileInfo
        // all SplFileInfo's methods are available
        stringln('filename: ' . $textFile->getFilename());
        stringln('------------------------------');
        foreach ($textFile as $line) {
            stringln($line);
        }

        $textFile->seek(3);
        stringln('fourth line:' . $textFile->current());

        // move cursor to the end of file
        while(!$textFile->eof()) {
            $textFile->next();
        }
        stringln('adding new line:' . $textFile->fwrite("newly added stuff\r\n"));

        stringln('');
    }
}

// working with CSV
$csvFile = new SplFileObject('../common/data/cars_tab.csv');
$csvFile->setFlags(SplFileObject::READ_CSV);
$csvFile->setCsvControl("\t"); // set delimeter to tabs(defualt is ,)

foreach ($csvFile as $line) {
    writeln($line);
}


/**
 * complete methods list(extended methods omitted):
 *
 * current()
 * oef()
 * fflush()
 * fgetc()
 * fgetcsv()
 * fgets()
 * fgetss()
 * flock()
 * fputcsv()
 * fread()
 * fscanf()
 * fseek()
 * fstat()
 * ftell()
 * ftruncate()
 * fwrite()
 * getChildren()
 * getCsvControl()
 * getFlags()
 * getmaxLineLen()
 * hasChildren()
 * key()
 * next()
 * rewind()
 * seek()
 * setCsvControl()
 * setFlags()
 * setMaxLineLen()
 * valid()
 */