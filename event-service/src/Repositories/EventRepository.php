<?php

namespace App\Repositories;

class EventRepository {
    private $filePath;

    public function __construct() {
        $this->filePath = __DIR__ . '/../../data/events.json';
    }

    public function save($data) {
        $events = $this->readData();
        $events[] = $data;
        file_put_contents($this->filePath, json_encode($events, JSON_PRETTY_PRINT));
    }

    public function getEvents() {
        return $this->readData();
    }

    private function readData() {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $json = file_get_contents($this->filePath);
        return json_decode($json, true);
    }
}
