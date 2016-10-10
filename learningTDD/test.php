<?php
class SomeTest extends PHPUnit_Framework_TestCase {
    /**
     * @expectedException Exception
     */
    public function testSomething() {
        throw new Exception;
    }
}