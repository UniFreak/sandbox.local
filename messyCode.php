<?php

namespace my\only;

class Cls
{
    public $someAttrbute;
    private $niceIndentation;

    public function demo(
        $params1 = '',
        $params2 = '',
        $params3 = '',
        $and = '',
        $more = '',
        $and2 = '',
        $never = '',
        $end = ''
    ) {
        $ary = [1, 2, 3, 5, 6, [
            'just' => 'for',
            'demo' => 'only',
        ]];

        self::debugMe();
    }

    public static function debugMe()
    {
        $a = 'b';
        $b = 'c';
        echo $a . $b;
    }
}
$cls = new Cls();
$cls->demo();
