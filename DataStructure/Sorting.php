<?php
// 冒泡排序
function bubbleImprovedSort($arr) {
    $len = count($arr);
    for($i = 0; $i < $len; $i ++) {
        $noswap = true; // 用于指示是否还需要继续判断, 默认不需要
        for($j = $len - 1; $j > $i; $j --) {
            if ($arr [$j] > $arr [$j - 1]) {
                $tmp = $arr [$j - 1];
                $arr [$j - 1] = $arr [$j];
                $arr [$j] = $tmp;
                $noswap = false; // 如果做出了排序更改, 则设为需要再判断
            }
        }
        if ($noswap) { // 检查是否需要继续判断, 否则返回数组
            return $arr;
        }
    }
}
print_r(bubbleImprovedSort([10, 2, 36, 25, 5]));

// 快速排序
function partition(&$arr, $low, $high)
{
    $pivot = $arr[$low];
    while ($low < $high) {
        while ($low < $high && $arr[$high] >= $pivot) {
            $high--;
        }
        $arr[$low] = $arr[$high];

        while ($low < $high && $arr[$low] <= $pivot) {
            $low++;
        }
        $arr[$high] = $arr[$low];

    }
    $arr[$low] = $pivot;
    return $low;
}

function quickSort(&$arr, $low, $high)
{
    if ($low < $high) {
        $pivot = partition($arr, $low, $high);
        quickSort($arr, $low, $pivot-1);
        quickSort($arr, $pivot+1, $high);
    }
}

$arr = [2, 5, 8, 3, 10, 1, 8, 7, 6];
quickSort($arr, 0, count($arr) - 1);
print_r($arr);