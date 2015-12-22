<?php
try {
    throw new Exception('messg', 1234);
} catch (Exception $e) {
    var_dump($e->getCode());
    var_dump($e->getMessage());
}