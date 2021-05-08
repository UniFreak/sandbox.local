<?php
require '../utils.php';

$testFile = './common/test.txt';
writeln(basename($testFile));
writeln(dirname($testFile));
writeln(pathinfo($testFile));

writeln($stat = stat($testFile));

writeln(filegroup($testFile));
writeln(chgrp($testFile, 1)); // false
writeln(chown($testFile, 'root'));

rename($testFile, './common/copied.txt');


// writeln(posix_getpwuid($stat['uid'])); // posix_* not avail on windows

writeln(sprintf('%o', fileperms($testFile)));
chmod($testFile, 0777);
clearstatcache();
writeln(sprintf('%o', fileperms($testFile)));

