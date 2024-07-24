<?php

namespace App\Controllers;

use App\Services\EventService;

class EventController {
    private $eventService;

    public function __construct() {
        $this->eventService = new EventService();
    }

    public function handleRequest() {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $this->eventService->handleEvent($data);
                echo json_encode(['message' => 'Event processed']);
                break;
        }
    }
}
