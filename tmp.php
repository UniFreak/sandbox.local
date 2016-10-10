<?php
function binSearch($arr, $val)
{
    $low = 0;
    $high = count($arr) - 1;
    while ($low <= $high) {
        $mid = (int) ($low + $high) / 2;
        if ($val == $arr[$mid]) {
            return $mid;
        } else if ($val < $arr[$mid]) {
            $high = $mid - 1;
        } else {
            $low = $mid + 1;
        }
    }
    return 0;
}

echo binSearch([1, 2, 3, 4, 5, 8], 6);