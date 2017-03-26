<?php
class Command {
    const VERSION = '0.0.1';

    public function printVersion() {
        echo 'the version is:' . self::VERSION;
    }
}

class OutputTest extends PHPUnit_Framework_TestCase {
    public function testPrintVerion() {
        // $this->expectOutputString('the version is:0.0.1');
        $this->expectOutputRegex('/the version is/');

        $command = new Command();
        $command->printVersion();
    }
}