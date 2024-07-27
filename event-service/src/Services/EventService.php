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
        if (isset($event['type'], $event['data'])) {
            $this->eventRepository->save($event['type'], $event['data']);
            $this->eventListener->processEvent($event);
        } else {
            echo json_encode(['message' => 'Invalid event structure'], JSON_THROW_ON_ERROR, 400);
        }
    }
}
