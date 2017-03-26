<?php
echo 'Host:' . $_SERVER['HTTP_HOST'] . "\n";
echo 'Url:' . $_SERVER['REQUEST_URI'] . "\n";
echo 'Mehotd:' . $_SERVER['REQUEST_METHOD'] . "\n";
echo 'Accept:' . $_SERVER['HTTP_ACCEPT'] . "\n";

echo 'Query String Params:' . var_export($_GET, true) . "\n";

$body = file_get_contents('php://input');
print_r($body);
$posts = array();
parse_str($body, $posts);
echo 'Fields Posted to the body:' . var_export($posts, true) . "\n";