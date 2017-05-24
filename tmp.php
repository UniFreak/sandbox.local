<?php
class Father
{
    const A = 'a';
}

class Child extends Father
{
    public function __construct() {
        echo self::A;
    }
}

new Child();