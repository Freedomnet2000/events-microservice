<?php

namespace App\Utils;

class EventPublisher {
    public function publish($eventType, $data) {
        $event = ['type' => $eventType, 'data' => $data];
        file_get_contents('http://event-service/index.php', false, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($event),
            ],
        ]));
    }
}
