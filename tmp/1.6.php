<?php
$a = range(0, 99);
var_dump($a);
for ($i = 0; $i <= 99/2-1; $i ++) {
    $tmp = $a[$i];
    $a[$i] = $a[99 - $i];
    $a[99 - $i] = $tmp;
}
var_dump($a);