<?php

namespace App\Utils;

class EventPublisher {
    public function publish($eventType, $data) {
        $event = ['type' => $eventType, 'data' => $data];
        file_get_contents('http://localhost:8002/index.php', false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($event),
            ],
        ]));
    }
}
