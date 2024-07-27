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

    public function handleEvent($event, &$error) {
        $error = '';
        if (isset($event['type'], $event['data'])) {
            return $this->eventRepository->save($event['type'], $event['data'], $error) 
                && $this->eventListener->processEvent($event, $error);
        } else {
            $error = 'Invalid event structure';
            return false;
        }
    }
}
