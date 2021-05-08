<?php
class Events {
    protected $events = [
        [
            'name' => 'event 1',
            'date' => '2019',
        ],
        [
            'name' => 'event 2',
            'date' => '2020',
        ],
        [
            'name' => 'event 3',
            'date' => '2021',
        ],
    ];

    public function getEvents() {
        return $this->events;
    }

    public function getEventById($id) {
        return $this->events[$id];
    }
}