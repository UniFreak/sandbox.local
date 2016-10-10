<?php
/**
 * stack:last in, first out(LIFO)
 * queue:first in, first out(FIFO)
 *
 * SplStack and SplQueue both extends from SplDoublyLinkedList, so you do can
 * remove element from any side of them, but that defeat the meaning of stack
 * and queue
 */
include '../../utils.php';

$stack = new SplStack();
$stack[] = 'A';
$stack->push('B');
$stack->push('C');

$queue = new SplQueue();
$queue[] = 'A';
$queue->enqueue('B');
$queue->enqueue('C');

loop($stack, 'stack:'); // LIFO
loop($queue, 'queue:'); // FIFO

stringln('poped from stack:' . $stack->pop());
stringln('dequeued from queue:' . $queue->dequeue());

// auto delete element when iterating
$stack->setIteratorMode(SplStack::IT_MODE_DELETE | SplStack::IT_MODE_LIFO);
$queue->setIteratorMode(SplQueue::IT_MODE_DELETE | SplQueue::IT_MODE_FIFO);
loop($stack, 'stack again(auto delete):');
loop($queue, 'queue again(auto delete):');
if ($stack->isEmpty() && $queue->isEmpty()) {
    stringln('both now are empty');
}