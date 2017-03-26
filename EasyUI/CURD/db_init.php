<?php 
try {
    $db = new mysqli('localhost', 'root', '', 'easyui');
} catch (Exception $e) {
    echo 'connection failed: ' . $db->error;
    exit();
}