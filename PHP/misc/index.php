<?php
include('../../utils.php');

ignore_user_abort(true);

sleep(1); // sleep one second
usleep(1000); // and another
time_sleep_until(time() + 1); // and another
time_nanosleep(0, 50000000); // and another half second

// writeln('sys_getloadavg()');  // undefined on Windows

writeln(uniqid('prefix_'));

writeln('CONNECTION_ABORTED:' . CONNECTION_ABORTED);
writeln('CONNECTION_NORMAL:' . CONNECTION_NORMAL);
writeln('CONNECTION_TIMEOUT:' . CONNECTION_TIMEOUT);

writeln(connection_aborted());
writeln(connection_status());

defined('TEST_CONST') or define('TEST_CONST', 'test const');
writeln(constant('TEST_CONST'));

eval('writeln("1");');

// die();
// exit('existing');

// writeln(get_browser('')); // take too long time to execute

writeln(highlight_file('./example.php')); // alias: show_source()
writeln('');
writeln('<?php echo "test string";');
writeln('');
writeln(php_strip_whitespace('./example.php'));
writeln('');

$bin = pack("S", 65535);
$ray = unpack("S", $bin);
echo "UNSIGNED SHORT VAL = ", $ray[1], "\n";

$fp = fopen(__FILE__, 'r');
fseek($fp, __COMPILER_HALT_OFFSET__);
writeln(stream_get_contents($fp));
__halt_compiler();
the installation data (eg. tar, gz, PHP, etc.)