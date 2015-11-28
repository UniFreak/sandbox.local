<?php 
require 'db_init.php';

$stmt = $db->prepare('DELETE FROM users WHERE id = ?');
$stmt->bind_param('i', $_POST['id']);
$stmt->execute();
$stmt->close();
$db->close();