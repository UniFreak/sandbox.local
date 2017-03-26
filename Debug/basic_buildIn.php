<?php 
/**
 * Error type
 *     - Syntax error
 *     - Logical/semantic error
 * Error severities
 *     - notice
 *     - warning
 *     - error
 * When error occur
 *     - compile time
 *     - run time
 * Common error level
 *     - E_ERROR
 *     - E_WARNING
 *     - E_PARSE
 *     - E_NOTICE
 *     - E_STRICT
 *     - E_DEPERACATED
 *     - E_ALL
 * User error level(triggered by `trigger_error`)
 *     - E_USER_NOTICE
 *     - E_USER_WARNING
 *     - E_USER_ERROR
 * 
 * error reporting default to `all except E_NOTICE and E_STRICT`
 * error dispaly default to off
 *
 * you can set error reproting or error logging in
 *     - php.ini
 *     - httpd.conf
 *     - .htaccess
 *     - run time
 *
 * Best practice
 *     - do *not* show debug msg in production env
 *     - do *not* log PHP error in Apache error logs
 *     - set log file location out of web root
 *     - user a shutdown function
 */

// dispaly error and setup error_reporting level
ini_set('display_errors', 1);
error_reporting(E_ALL | E_NOTICE);

// enable error logging
// NOTE: only work when there is no parse error
ini_set('log_errors', 1);
// unlimit error log length
ini_set('log_errors_max_length', 0);
// specify log file location(this is only for demo, remember the best practice!)
ini_set('error_log', './error.log');

// debug_backtrace(only work if there is no fatal error, for fatal error, use Xdebug)
function a() {
    b('beta');
}
function b() {
    c('celta');
}
function c() {
    var_dump(debug_backtrace());
}
c();

/**
 * shutdown function
 *
 * email web master when script terminated by error
 * only work if there is no parse error
 */
function shutdownNotify() {
    // get last error
    $err = error_get_last();
    if (!empty($err) && in_array($err['type'], array(E_ERROR,E_USER_ERROR))) {
        echo '<h1>Yea. Sry, SMTing wt wrng</h1>';
        $to = 'webmaster@example.com';
        $subject = 'your server went wrong';
        $msg = var_export($err, true) . PHP_EOL;
        $msg .= var_export($_SERVER, true) . PHP_EOL;
        mail($to, $subject, $msg);
    }
}
// and register the shutdown function
register_shutdown_function('shutdownNotify');

// trigger user error
// useful if you wanna log something in error log
trigger_error('user notice', E_USER_NOTICE);
trigger_error('user warning', E_USER_WARNING);
trigger_error('user error', E_USER_ERROR);