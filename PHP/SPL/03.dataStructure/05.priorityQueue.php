<?php
/**
 * SplPriorityQueue can let you order things by assign them an priority value, the
 * higher the value, the higher is the element is placed in the heap
 *
 * a typical use case: inspecting php error log, order by their severity
 */
include '../../utils.php';

function setPriority($line) {
    $start = strpos($line, 'PHP');
    $end = strpos($line, ':', $start);
    $error = substr($line, $start, $end-$start);

    switch ($error) {
        case 'PHP Fatal error':
        case 'PHP Catchable fatal error':
            return 10;
        case 'PHP Warning':
            return 8;
        case 'PHP Deprecated':
            return 7;
        case 'PHP Parse error':
            return 5;
        case 'PHP Notice':
            return 2;
        default:
            return 0;
    }
}

// ==================== non ordered ====================
$log = new SplPriorityQueue();
$file = new SplFileObject('C:/Apache24/logs/sandbox.local-error.log');
while (!$file->eof()) {
    $line = $file->fgets();
    $log->insert($line, setPriority($line));
}
while (!$log->isEmpty()) {
    stringln($log->extract());
}


// ==================== ordered ====================
/**
 * because SplPriorityQueue is a heap, the order in group are not in origin order
 * if you need that, you need to extend SplPriorityQueue
 */
class OrderedPriority extends SplPriorityQueue
{
    protected $serial = PHP_INT_MAX;

    public function insert($value, $priority)
    {
        // how this work: when inserting new element, it first look at the
        // priority passed in, then look at the serial passed(minus one every time)
        // hence preserve the origin order
        parent::insert($value, array($priority, $this->serial--));
    }
}

$orderdLog = new OrderedPriority();
$file = new SplFileObject('C:/Apache24/logs/sandbox.local-error.log');
while (!$file->eof()) {
    $line = $file->fgets();
    $orderdLog->insert($line, setPriority($line));
}
while (!$orderdLog->isEmpty()) {
    stringln($orderdLog->extract());
}