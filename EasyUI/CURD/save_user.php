<?php 
require 'db_init.php';

$stmt = $db->stmt_init();
if ($stmt->prepare('INSERT INTO users (firstname, lastname, phone, email) VALUES (?, ?, ?, ?)')) {
    echo 'prepared<br />';
    $stmt->bind_param('ssss', $_POST['firstname'], $_POST['lastname'],
                    $_POST['phone'], $_POST['email']);
    $stmt->execute();
    $stmt->close();
} else {
    echo 'error while inserting new user: ' . $stmt->error;
}

$db->close();