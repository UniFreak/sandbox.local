<?php
function eVar($var) {
    echo $var . '<br />';
}
function dVar($var) {
    var_dump($var);
}

// ==================== Date/Time ====================
eVar(time());
eVar(microtime());
eVar(microtime(true));

eVar(strtotime('22nd December 1979'));
// note strtotime will consider 10/11/2003 as year 2003 month 10 day 11
eVar(strtotime('1979/12/22'));
eVar(strtotime('22 Dec. 1979 17:30'));
// return false if it can't parse to timestamp
dVar(strtotime('Christmas 1979'));
// you can get relative time
eVar(strtotime('Next Sudnay', time() - (86400 * 2)));
// if you don't pass the second parameter, it assume relative to now
eVar(strtotime('Next Sunday'));

eVar(date('Y-m-d H:i:s'));
// notice the double escaping ..., it hurt
eVar(date("\M\y b\i\\r\\t\h\d\a\y \i\s \o\\n \a l \\t\h\i\s \y\\e\a\\r. ", strtotime("22 Dec 2004")));

// hour, minute, second, month, day, year and isDaylighSaving
eVar(mktime(22, 30, 0, 20, 6, 2005, -1));


// ==================== Math ====================
eVar(ceil(4.9));
eVar(floor(4.9));
eVar(round(4.9125, 3));

// rand is less random(but more quidk) than mt_rand(mt means `mersenne twister`)
// if you don't pass parameter into it, it will generate from 1 to getrandmax()
eVar(rand());
eVar(getrandmax());
eVar(mt_rand(1,100));
eVar(mt_getrandmax());

// if you need the random number the same every time, pass a same `seed` to mt_srand()
mt_srand(123456);
eVar(mt_rand(1, 100));
eVar(mt_rand(1, 100));
eVar(mt_rand(1, 100));

// sin(),cos() and tan() take a radiant as parameter
// radiant is degrees multiplied by PI then divided by 180, deg2rad() can do it for you
eVar(sin(deg2rad(80)));
eVar(cos(89));
eVar(tan(45));
// asin(),acos() and atan() is the opposite of above three
// same as rad2deg()
eVar(rad2deg(asin(sin(deg2rad(80)))));

eVar(abs(-5));
eVar(sqrt(25)); // aka `square root`
eVar(pow(2, 3));// aka `power`
eVar(hypot(3, 4));// aka `hypotenuse`

// base_convert is the boss, now supported max base is 36
eVar(base_convert(10100, 2, 10));
eVar(bindec(10100));
eVar(decbin(5));
eVar(dechex(12));
eVar(hexdec('f'));
eVar(decoct(5));
eVar(octdec(8));

// here is comman math constants
eVar(M_PI); // Pi
eVar(M_PI_2); // Pi/2
eVar(M_PI_4); // Pi/4
eVar(M_1_PI); // 1/Pi
eVar(M_2_PI); // 2/Pi
eVar(M_SQRTPI); // sqrt(M_PI)
eVar(M_2_SQRTPI); // 2/sqrt(M_PI)
eVar(M_SQRT2); // sqrt(2)
eVar(M_SQRT3); // sqrt(3)
eVar(M_SQRT1_2); // 1/sqrt(2)

// ==================== String ====================
/** below are all for single byte(ASCII) string
 * for multibyte(non-English character), use their equivalents `mb_` functions
 * like: mb_strtoupper(), mb_strlen(), mb_ereg_match()
 *
 * or you can set `mbstring.func_overload` in php.ini
 * 1: mail() overloaded by mb_send_mail()
 * 2: functions staring with 'str' overloaded with their multibyte partner
 * 4: all the 'ereg' function are overloaded
 * you can also combine these together simply by add them up, such as
 * 3: mail() and str got overloaded
 * 7: all got overloaded
 * ...
 *
 * PHP 7 will full support for unicode
 */
eVar(substr('hello world', 0, -2));

eVar(str_replace('monkey', 'human', 'monkey is dumb'));
// the fourth param is a passed by reference, will be the count of find and replace
str_replace('had', 'foo', 'he had had to have had it.', $count);
eVar($count);
eVar(str_ireplace('Monkey', 'human', 'monkey is dumb'));

// chr: ascii to char, ord: char to ascii
eVar(ord(chr(109)));

eVar(strlen('hello world'));
// if you don't pass the second parameter, it return a array with 256 item
// in which every key is the ascii code, and the value is the count found in the string
dVar(count_chars('hello world'));
// pass the second parameter as `1`, the result will only contain chars that found
dVar(count_chars('hello world', 1));
// pass the second parameter as `2`, the result will only contain chars not found
dVar(count_chars('hello world', 2));
// return a number of how many world found in the string
eVar(str_word_count('hello world'));
// pass `1` return a array contain all found word
dVar(str_word_count('hello world, hello', 1));
// pass `2` return a array contain all found word, with index of the word as key
dVar(str_word_count('hello world, hello', 2));

// here is a common mistake: `This` is found in index 0, but 0 is considered false
// to solve this, use `=== false`
if (strpos('This is a test', 'This')) {
    echo 'found';
} else {
    echo 'not found';
}
// you can also pass a offset in the third parameter
eVar(strpos('This is a test', 'i', 3));
// case-insensitive version
eVar(stripos('This is a test', 't'));

eVar(strstr('http://www.example.com', 'www'));
eVar(stristr('http://WWW.EXAPMLE.COM', 'www'));

dVar(trim(' This is a test '));
// not this!
dVar(trim(' This is a test ', ' tes'));
dVar(ltrim(' This is a test '));
dVar(rtrim(' This is a test '));

eVar(wordwrap(' this is a very very long test and is certainly should be wrapped to let the user read it more conforteble', 20, '<br />'));

eVar(strtoupper('I love PHP'));
eVar(strtolower('I love PHP'));
eVar(ucfirst('I love PHP'));
eVar(ucwords('I love PHP'));

// sha1 hash are 40 byte long
// they are not reversible
// same hash for same string
// faster, less secure, so suitable for less secret data
eVar(sha1('Hello'));
eVar(sha1('Hello'));
// md5(aka message digest) is similar to sha1
// only that it is 32 byte long
eVar(md5('Hello'));
eVar(md5('Hello'));
// password_hash's result contain seed and algrithm info
// same string different hash, this make rainbow attack meaningless
// slower, more secure
// use password_verify() to verify
eVar(password_hash('Hello', PASSWORD_DEFAULT));
eVar($hash = password_hash('Hello', PASSWORD_DEFAULT));
if (password_verify('Hello', $hash)) {
    echo 'Verified';
}

// use mysql_real_escape_string instead for database security
eVar(addslashes('I\'m fine'));
eVar(stripslashes('I\'m fine'));
eVar(strip_tags('<h1>the site sucks</h1>'));

eVar(number_format(12345.6789));
eVar(number_format(12345.6789, 3));
eVar(number_format(12345.6789, 3, ',', '.'));

// 1: first string come before second
// -1: first string come after second
// 0: they are the same
eVar(strcmp('hello', 'Hello'));
eVar(strcasecmp('hello', 'Hello'));

eVar(str_pad('hello', 10, '*', STR_PAD_BOTH));
eVar(str_pad('hello', 10, '*', STR_PAD_LEFT));
eVar(str_pad('hello', 10, '*', STR_PAD_RIGHT));

$number = 123;
// available: %%, %b, %c, %d, %f, %o, %s, %x, %X
// it also support things like `%.2f` just as in C
eVar(printf("123 in binary is: %b", $number));
eVar(printf("123 in hex is: %h", $number));
eVar(printf("123 as a string is: %s", $number));
eVar(printf("%% allows you to print percent characters"));

// * similar to extract()
parse_str('foo=foo&bar=bar');
eVar($foo);
eVar($bar);
parse_str('foo=bar&bar=foo', $parseResult);
dVar($parseResult);

// ==================== REGEX ====================
// use it only when you have to, prefer the string functions

// return 1 if found pattern, else 0
eVar(preg_match('/.oo/', 'foo boo'));
// this will return the all found number
eVar(preg_match_all('/.oo/', 'foo boo'));
// you can pass the third parameter to store the matche result
eVar(preg_match_all('/.oo/', 'foo boo', $matches));
dVar($matches);

eVar(preg_replace('/[A-z]oo\b/', 'got word: $0 <br />', 'Foo moo boo tool foo'));
// use preg_replace_callback() instead of `e` modifier
eVar(preg_replace('/[A-z]oo\b/e', 'strtoupper("$0")', 'Foo moo boo tool foo', 2));


// ==================== OTHER ====================
eVar(function_exists('var_dump'));

$loaded = get_loaded_extensions();
foreach ($loaded as $extension) {
    echo '<b>' . $extension . '</b>';
    if (is_array(get_extension_funcs($extension))) {
        echo '(' . implode(',', get_extension_funcs($extension)) . ')<br />';
    } else {
        echo get_extension_funcs($extension);
    }
}
if (!extension_loaded('imap')) {
    dl('imap');     // dynamic load extensions
};

// sleep(4); // 4 seconds
// usleep(4); // 4 microseconds

// different to `exec`, paathru pass thru the ouput directely
print(exec('dir'));
exec('dir', $output, $result);
echo '`dir` return ' . $result . ' and output:<br />';
var_dump($output);
passthru('dir', $result);
echo($result);
// shell_exec() is the same as backtick ``
print_r(`dir`);
echo shell_exec('dir');
system('dir');
// use this to make it safe
escapeshellcmd('dir');

ignore_user_abort(true);
register_shutdown_function('displayConnStatus');
function displayConnStatus() {
    // 0: still alive, 1:timeout, 2: aborted, 3: aborted then timed out(only can hapenn with ignore_user_abort set to true)
    echo 'connection_status:' . connection_status() . '<br />';
    // echo 'connection_timeout:' . connection_timeout() . '<br />';
    echo 'connection_aborted:' . connection_aborted() . '<br />';
}

dVar(php_ini_loaded_file());
ini_set('max_execution_time', 0);
eVar(ini_get('max_execution_time'));
set_time_limit(0);

function showArgs() {
    for ($i = 0; $i < func_num_args(); $i++) {
        echo 'received param:' . func_get_arg($i) . '<br />';
    }

    var_dump(func_get_args());
}
showArgs('a', 'b', 'c');

// ==================== Variable function ====================
$func = 'str_replace';
if (is_callable($func)) {
    $output = call_user_func($func, 'monkeys', 'giraffes', 'many monkeys');
    $sameOutput = call_user_func_array($func, array('monkeys', 'gireffes', 'many monkeys'));
}
eVar($output);
eVar($sameOutput);

// ==================== declare & tick ====================
// ticks is deprecated
// a tick is a special event that occurs internally in PHP each time it has executed a certain number of statements
// ticks is not multi-threading, for that, see `process control`
function myfunc($param1, $param2) {
    eVar("In first tick function with params $param1 $param2\n");
}
function myfunc2($param1, $param2, $param3) {
    eVar("In second tick function with params $param1 $param2 $param3\n");
}
function myfunc3($param1) {
    eVar("In third tick function with params $param1\n");
}

register_tick_function("myfunc", "hello", "world");
register_tick_function("myfunc2", "how", "are", "you?");
register_tick_function("myfunc3", "goodbye!");
unregister_tick_function("myfunc2");

declare(ticks=10);
for($i = 0; $i < 20; ++$i) {
    eVar("Hello\n");
}

eVar(substr('我的', 0, 1));