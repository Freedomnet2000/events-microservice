<?php

namespace App\Services;

use App\Utils\EventListener;
use App\Repositories\EventRepository;


class EventService {
    private $eventListener;
    private $eventRepository;


    public function __construct() {
        $this->eventListener = new EventListener();
        $this->eventRepository = new EventRepository();

    }

    public function handleEvent($event) {
        $this->eventRepository->save($event);
        $this->eventListener->processEvent($event);
    }
}
