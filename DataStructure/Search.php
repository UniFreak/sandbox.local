<?php
/**
 * 顺序查找顺序表
 */
function seqSearch($array, $val)
{
    $array[] = $val;

    $i = 0;
    while ($array[$i] != $val) {
        $i++;
    }

    if ($i < (count($array)-1)) {
        return $i;
    }
    return -1;
}

/**
 * 二分查找有序表
 */
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

