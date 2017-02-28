<?php
require '../utils.php';

// ==================== Constans ====================
writeln(DIRECTORY_SEPARATOR);
writeln(PATH_SEPARATOR);

// ==================== Function ====================
writeln(getcwd());
chdir('../');
writeln(getcwd());
// chroot('../'); // not available on windows
// writeln(getcwd());

$dir = getcwd();
if (is_dir($dir)) {
    if ($dirHandle = opendir($dir)) {
        while (($file = readdir($dirHandle)) !== false) {
            writeln($file);
        }

        writeln(readdir($dirHandle)); // false
        rewinddir($dirHandle);
        writeln(readdir($dirHandle));

        closedir($dirHandle);
    }
}

writeln(scandir($dir));

// ==================== OO ====================
$dirObject = dir($dir);
writeln($dirObject);
while (($file = $dirObject->read()) != false) {
    writeln($file);
}

writeln($dirObject->read()); // false
$dirObject->rewind();
writeln($dirObject->read());

$dirObject->close();