<?php

namespace App\Services;

use App\Utils\EventListener;

class EventService {
    private $eventListener;

    public function __construct() {
        $this->eventListener = new EventListener();
    }

    public function handleEvent($event) {
        $this->eventListener->processEvent($event);
    }
}
