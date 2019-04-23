<?php

namespace Processor;

class TimerProcessor
{
    private $last;

    public function __construct()
    {
        $this->last = microtime(true);
    }

    public function __invoke(array $record)
    {
        $now = microtime(true);
        $record['extra']['time_total'] = $now;
        $record['extra']['time_cost'] = number_format($now - $this->last, 3);
        $this->last = $now;

        return $record;
    }
}