<?php
/**
 * 1. doing something like this when `register_globals` is on
 *
 * hack: pass a `?authorized=1` in URL
 *
 * fix:
 *     - turn off `register_globals`
 *     - only use variable you declared, like:
 *         $authorized = 0;
 *         if ($passwrod == 'my_password') {
 *             $authorized = 1;
 *         }
 */
if ($password == 'my_password') {
    $authorized = 1;
}

if ($authorized) {
    // lots of secret stuff
}

/**
 * 2. show error message in production env
 */

/**
 * 3. SQL injection.
 *
 * hack: post `' OR 1=1 #` as username field, the final SQL will be
 *     `select username, password from users where username = '' OR 1=1 #' and password = ''`
 *
 * fix:
 *     - escape with `mysql_real_escape_string()`
 *     - use prepared statement
 */
mysql_query('select username, password from users where username = "' . $_POST['username'] . '" and password = "' . $_POST['password'] . '"');

/**
 * 4. include files according to query string, say `index.php?page=contactus.html`
 *
 * hack: change `contactus.html` to `.htpasswd`
 *
 * fix
 *     - set `open_basedir` properly and turn `allow_url_fopen` off
 *     - use a white file list to filter what's going to be included
 */

/**
 * 5. use default things and be very predictable. say `root` mysql user, `admin/` folder
 */

/**
 * 6. leave installation files online. they maybe used by other to overwrite your entrie website
 */