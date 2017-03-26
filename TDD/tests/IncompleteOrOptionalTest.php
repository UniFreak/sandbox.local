<?php
class IncompleteOrOptionalTest extends PHPUnit_Framework_TestCase {
    public function testIncomplete() {
        $this->markTestIncomplete('waiting for implementation');
    }

    public function testOptional() {
        if (!extension_loaded('memcache')) {
            $this->markTestSkipped('Install memcache');
        }
    }
}