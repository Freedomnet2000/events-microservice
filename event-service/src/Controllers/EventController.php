<?php

namespace App\Controllers;

use App\Services\EventService;

class EventController {
    private $eventService;

    public function __construct() {
        $this->eventService = new EventService();
    }

    public function handleRequest() {
        $error = '';
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $this->eventService->handleEvent($data, $error);
                if($result) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => $error]);
                }
                break;
        }
    }
}
