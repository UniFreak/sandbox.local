<?php
function eVar($var) {
    echo $var . '<br />';
}
function dVar($var) {
    var_dump($var);
}

/**
 * ==================== writing files ====================
 */
$lengthWriten = file_put_contents(
    'assets/aFile.txt',
    "\nString writen by file_put_contents(filename, data)\nAnd the second line",
    FILE_APPEND
    );
eVar('got ' . $lengthWriten . ' Bytes writen using file_put_contents');

// check if already exists, if not:
if (!file_exists('assets/aFile.txt')) {
    // create it
    touch('assets/aFile.txt');
}
// *best practice is to always use b
$theFile = fopen('assets/aFile.txt', 'wb');
// third parameter is max-length to write
$lengthWriten = fwrite($theFile, "\nString writen by fwrite");
// rewind the file pointer to the file begining
rewind($theFile);
fwrite($theFile, "I'm actually writen later");
// move file pointer to anywhere, parameter two is byte-wise offset
// parameter three can be: SEEK_SET, SEEK_CUR, SEEK_END, to specify from where
// to begin the seek
fseek($theFile, -10, SEEK_CUR);
fwrite($theFile, "(I'm more later... shhhh)");
// *always close the file handler
fclose($theFile);

/**
 * ==================== reading files ====================
 */
// if you only want print a file directly
// doesn't work with binary file
readfile('assets/aFile.txt');

// return a whole string
dVar(file_get_contents('assets/aFile.txt'));
// return a array each item contain a line of the file
dVar(file('assets/aFile.txt'));

// availabel second parameter is: w(rite), r(ead), a(ppend), b(inary-safe)
$theFile = fopen('assets/aFile.txt', 'rb');
// the second parameter: how many Bytes you wanna read
// and filesize() can help you to determine a file's Bytes length
$content = fread($theFile, filesize('assets/aFile.txt') / 2);
fclose($theFile);
dVar($content);

/**
 * ==================== mving, cping, and deleting ====================
 */
// don't use this to move uploaded file, for that, use `move_uploaded_file()` instead
// it will make sure the file is indeed been uploaded(as `is_uploaded_file()` do)
if (file_exists('assets/old.txt')) {
    $originFile = 'assets/old.txt';
    $newFile = 'assets/new.txt';
} else {
    $originFile = 'assets/new.txt';
    $newFile = 'assets/old.txt';
}
$copy = $newFile . '.cp';

rename($originFile, $newFile);
copy($newFile, $copy);
// you need to `fclose()` the file before you can `unlink()` it
unlink($copy);

/**
 * ==================== temp file ====================
 * think a temp file as your scratchpad
 * the usage of it is the same as regular file
 */
$tmp = tmpfile();
$byteWriten = fwrite($tmp, 'used as it\'s a clipboard');
eVar($byteWriten . ' bytes has been writen into tmp file');
fclose($tmp);
// if you want to know where the tmp file is stored:
eVar(sys_get_temp_dir());

/**
 * ==================== checking file status ====================
 * commonly used before manipulating a file, to eliminate errors
 */
$theFile = 'assets/aPic.jpg';
// chmod only work in unix like OS
chmod($theFile, 0755);
// chown is rarely used, becuase it require special privilege
// and only crazy people run php with such high privilege
chown($theFile, 'dummy');

eVar($theFile . ' is file?' . (int)is_file($theFile));
eVar($theFile . ' is dir?' . (int)is_dir($theFile));
eVar($theFile . ' is readable?' . (int)is_readable($theFile));
eVar($theFile . ' is writable?' . (int)is_writable($theFile));
eVar($theFile . ' is executable?' . (int)is_executable($theFile));
// all above functions' result are cached, if you need to be sure, use:
clearstatcache('assets/aPic.jpg');

/**
 * ==================== dissect filename info ====================
 */
dVar(pathinfo($theFile));
// the second parameter is a suffix, say you pass 'php', then if the file is *.php
// it will auto remove the suffix 'php'
dVar(basename($theFile));

/**
 * ==================== file lock ====================
 * on unix. `flock()` is advisory, means OS is free to ignore it
 * on windows, `flock()` is mandatory,
 * means file are locked by the OS whether you ask it or not
 *
 * file lock only work on modern file system such as NTFS,ext3/4,HFS
 * not with NFS
 *
 * LOCK_SH: share lock, to read
 * LOCK_EX: exclusive lock, to write
 * LOCK_UN: unlock, to release the lock
 * you can add `| LOCK_NB`(non-block) to prevent php from hang there if can't
 * get the lock, instead to return false immediately
 */
$theFile = fopen($theFile, 'rb');
flock($theFile, LOCK_SH | LOCK_NB);
flock($theFile, LOCK_UN);
fclose($theFile);

/**
 * ==================== Checksum ====================
 * to ensure user downloaded valid file, or a file is official
 */
eVar(sha1_file('assets/aFile.txt'));
eVar(md5_file('assets/aFile.txt'));

/**
 * ==================== parse ini file ====================
 * ini file format generally is for saving configuration
 * mind the security when using ini file, place it in a secret place
 */
define('BINARY', 'save as binary');
// it will auto expand constant value(if not quoted)
dVar(parse_ini_file('assets/iniFile.ini'));
// true will let this function return array with section header(things in [])
dVar(parse_ini_file('assets/iniFile.ini', true));

/**
 * ==================== Working with directory ====================
 */
$dir = opendir('.');
if ($dir) {
    while (false != ($file = readdir($dir))) {
        eVar($file);
    }
    closedir($dir);
}

// pass 1 to sort reversely
dVar(scandir('.', 1));

// this requre the directory is not empty
rmdir('.');