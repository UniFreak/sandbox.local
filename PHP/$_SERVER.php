<?php
var_dump('===============================');
var_dump($_SERVER['argv']);
var_dump($_SERVER['argc']);
var_dump($_SERVER['GATEWAY_INTERFACE']);

var_dump('===============================');
var_dump($_SERVER['SERVER_ADDR']);
var_dump($_SERVER['SERVER_NAME']);
var_dump($_SERVER['SERVER_SOFTWARE']);
var_dump($_SERVER['SERVER_PROTOCOL']);

var_dump('===============================');
var_dump($_SERVER['REQUEST_URI']);
var_dump($_SERVER['REQUEST_METHOD']);
var_dump($_SERVER['REQUEST_TIME']);
var_dump($_SERVER['REQUEST_TIME_FLOAT']);
var_dump($_SERVER['QUERY_STRING']);
var_dump($_SERVER['DOCUMENT_ROOT']);

var_dump('===============================');
var_dump($_SERVER['HTTP_ACCEPT']);
var_dump($_SERVER['HTTP_ACCEPT_CHARSET']);
var_dump($_SERVER['HTTP_ACCEPT_ENCODING']);
var_dump($_SERVER['HTTP_ACCEPT_LANGUAGE']);
var_dump($_SERVER['HTTP_CONNECTION']);
var_dump($_SERVER['HTTP_HOST']);
var_dump($_SERVER['HTTP_REFERER']);
var_dump($_SERVER['HTTP_USER_AGENT']);
var_dump($_SERVER['HTTP_HTTPS']);

var_dump('===============================');
var_dump($_SERVER['REMOTE_ADDR']);
var_dump($_SERVER['REMOTE_HOST']);
var_dump($_SERVER['REMOTE_PORT']);
var_dump($_SERVER['REMOTE_USER']);
var_dump($_SERVER['REDIRECT_REMOTE_USER']);

var_dump('===============================');
var_dump($_SERVER['SCRIPT_FILENAME']);
var_dump($_SERVER['SCRIPT_NAME']);

var_dump('===============================');
var_dump($_SERVER['SERVER_ADMIN']);
var_dump($_SERVER['SERVER_PORT']);
var_dump($_SERVER['SERVER_SIGNATURE']);

var_dump('===============================');
var_dump($_SERVER['PATH_TRANSLATED']);
var_dump($_SERVER['PATH_INFO']);
var_dump($_SERVER['ORIG_PATH_INFO']);

var_dump('===============================');
var_dump($_SERVER['PHP_AUTH_DIGEST']);
var_dump($_SERVER['PHP_AUTH_USER']);
var_dump($_SERVER['PHP_AUTH_PW']);
var_dump($_SERVER['PHP_AUTH_TYPE']);
include 'toInclude.php';
var_dump($_SERVER['PHP_SELF']);
var_dump(__FILE__);