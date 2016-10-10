<?php
$str = 'demo string';

// 设计此种编码是为了使二进制数据可以通过非纯 8-bit 的传输层传输
// 例如电子邮件的主体
// Base64-encoded 数据要比原始数据多占用 33% 左右的空间
echo $encoded64 = base64_encode($str) . "\n";
echo $decoded64 = base64_decode($encoded64) . "\n";

$baidu = 'http://www.baidu.com';
print_r(get_headers($baidu));
print_r(get_headers($baidu));

$bing = 'http://bing.com';
print_r(get_meta_tags($bing));

$queryAry = array(
    'num',
    'named' => 'value',
    'parent' => array(
        'son' => 'bart simpson',
        'lisa',
    ),
);
print_r(http_build_query($queryAry, 'pre_', '###', PHP_QUERY_RFC3986));

$url = 'http://username:~pass word@hostname/path?arg=value#anchor';
print_r(parse_url($url));
echo parse_url($url, PHP_URL_PATH) . "\n";

// 除了 -_. 之外的所有非字母数字字符都将被替换成百分号(%)后跟两位十六进制数
// 空格则编码为加号(+)
// rawurlencode vs urlencode: 空格 和 ~
echo $encodedUrl = urlencode($url) . "\n";
echo $encodedRaw = rawurlencode($url) . "\n";
echo $decodedUrl = urldecode($encodedUrl) . "\n";
echo $decodedRaw = rawurldecode($encodedRaw) . "\n";