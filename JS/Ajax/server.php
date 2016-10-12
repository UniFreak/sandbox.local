<?php
$response = print_r(getallheaders(), true);
$response .= print_r($_POST, true);
echo $response;