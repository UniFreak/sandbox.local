<?php
// ==================== Exception ====================
try {
    throw new Exception('exception', 0);
} catch(Exception $e) {
    print_r($e);
    print_r($e->getMessage() . "\n");
    print_r($e->getPrevious() . "\n");
    print_r($e->getCode() . "\n");
    print_r($e->getFile() . "\n");
    print_r($e->getLine() . "\n");
    print_r($e->getTrace() . "\n");
    print_r($e->getTraceAsString() . "\n");
}

// ==================== ErrorException ====================
try {
    throw new ErrorException('error exception', 0, 1);
} catch(Exception $e) {
    print_r($e);
    print_r($e->getMessage() . "\n");
    print_r($e->getPrevious() . "\n");
    print_r($e->getCode() . "\n");
    print_r($e->getSeverity() . "\n");
    print_r($e->getFile() . "\n");
    print_r($e->getLine() . "\n");
    print_r($e->getTrace() . "\n");
    print_r($e->getTraceAsString() . "\n");
}

// ==================== SPL  ====================
/**
 * LogicException
 *   BadFunctionCallException
 *      BadMethodCallException
 *
 *   DomainException
 *   InvalidArgumentException
 *   LengthException
 *   OutOfRangeException
 *
 * RuntimeException
 *   OutOfBoundsException
 *   OverflowException
 *   RangeExcpetion
 *   UnderflowException
 *   UnexpectedValueException
 */