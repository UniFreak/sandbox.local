<?php 
require 'db_init.php';
$db->set_charset('utf8');
$result = $db->query('select * from users;');
$rt = [];
while ($row = $result->fetch_assoc()) {
    $rt[] = $row;
}

echo json_encode($rt);