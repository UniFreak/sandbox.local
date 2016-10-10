<?php
function slowFact($n)
{
    $ret = 0;
    $multiCnt = 0;
    for ($i = 1; $i <= $n; $i++) {
        $tmp = 1;
        for ($j = 1; $j <= $i; $j++) {
            $tmp = $tmp * $j;
            $multiCnt++;
        }
        $ret += $tmp;
    }

    return array($ret, $multiCnt);
}

function fastFact($n)
{
    $ret = 0;
    $tmp = 1;
    for ($i = 1; $i <= $n; $i++) {
        $tmp = $tmp * $i;
        $ret += $tmp;
    }

    return $ret;
}

var_dump(slowFact(5));
var_dump(fastFact(5));