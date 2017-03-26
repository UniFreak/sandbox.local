<?php 
require 'db_init.php';

$stmt = $db->stmt_init();
if ($stmt->prepare('UPDATE users SET firstname=?, lastname=?, phone=?, email=? WHERE id=?')) {
    $stmt->bind_param('ssssi', $_POST['firstname'], $_POST['lastname'], $_POST['phone'], 
                    $_POST['email'], $_GET['id']);
    if (!$stmt->execute()) {
        throw new Exception('erorr while updating user ' . $_POST['id']);
    };
}
