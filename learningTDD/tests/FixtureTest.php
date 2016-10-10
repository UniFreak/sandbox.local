<?php
class FixtureTest extends PHPUnit_Framework_TestCase {
    public static function setUpBeforeClass() {
        echo 'this function runs before all tests, you can set up database or other states to prepare your tests' . PHP_EOL;
    }

    public static function tearDownAfterClass() {
        echo 'this function runs after all tests, you can reset database or other states to clear the mess here' . PHP_EOL;
    }

    public function setUp() {
        echo 'this run before every test' . PHP_EOL;
    }

    public function tearDown() {
        echo 'this run after every test' . PHP_EOL;
    }

    public function testNumberOne() {

    }

    public function testNumberTwo() {

    }
}