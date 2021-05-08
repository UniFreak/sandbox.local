<?php
// 自定义流过滤器需要
// - 扩展 php_user_filter 类
// - 实现 filter(), onCreate(), onClose()
// - 使用 stream_filter_register() 注册
//
// 流会把数据分成按次序排列的桶, 每个桶中的流数据量是固定的. 桶队列中的每个桶具有两个公开属性:
// data 和 datalen

// ==================== strtoupper filter ====================
class strtoupper_filter extends php_user_filter {
    public $stream;

    function filter($in, $out, &$consumed, $closing) {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = strtoupper($bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }
        // always append a terminating \n
        if ($closing) {
            $bucket = stream_bucket_new($this->stream, "\n");
            stream_bucket_append($out, $bucket);
        }
        return PSFS_PASS_ON;
    }
}

stream_filter_register("strtoupper", "strtoupper_filter")
or die("Failed to register filter");

$fp = fopen("common/toUpperFilter.txt", "w");
stream_filter_append($fp, "strtoupper");

fwrite($fp, "Line1\n");
fwrite($fp, "Word - 2\n");
fwrite($fp, "Easy As 123\n");

fclose($fp);
readfile("common/toUpperFilter.txt");

// ==================== generic filter ====================
class string_filter extends php_user_filter
{
    public $mode;

    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            if ($this->mode == 1) {
                $bucket->data = strtoupper($bucket->data);
            } elseif ($this->mode == 0) {
                $bucket->data = strtolower($bucket->data);
            }

            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }
        return PSFS_PASS_ON;
    }

    public function onCreate()
    {
        if ($this->filtername == 'str.toupper') {
            $this->mode = 1;
        } elseif ($this->filtername == 'str.tolower') {
            $this->mode = 0;
        } else {
            /* Some other str.* filter was asked for,
            report failure so that PHP will keep looking */
            return false;
        }

        return true;
    }
}

stream_filter_register("str.*", "string_filter")
or die("Failed to register filter");

$fp = fopen("common/genericFilter.txt", "w");
stream_filter_append($fp, "str.tolower");

fwrite($fp, "Line1\n");
fwrite($fp, "Word - 2\n");
fwrite($fp, "Easy As 123\n");

fclose($fp);
readfile("common/genericFilter.txt");

// ==================== reverse filter ====================
class reverse_filter extends php_user_filter {
    function filter($in, $out, &$consumed, $closing) {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $consumed += $bucket->datalen;
            $bucket->data = strrev($bucket->data);
            stream_bucket_prepend($out, $bucket);
        }
        return PSFS_PASS_ON;
    }
}

stream_filter_register("reverse", "reverse_filter");

$fp = fopen('common/reverseFilter.txt', 'w');
stream_filter_append($fp, 'reverse');

fwrite($fp, "line one \n");
fwrite($fp, "line two \n");
fwrite($fp, "line three \n");

fclose($fp);
readfile('common/reverseFilter.txt');