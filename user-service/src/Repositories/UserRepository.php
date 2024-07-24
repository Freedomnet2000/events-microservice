<?php

namespace App\Repositories;

class UserRepository {
    private $filePath;

    public function __construct() {
        $this->filePath = __DIR__ . '/../../data/users.json';
    }

    public function save($data) {
        $users = $this->readData();
        $users[] = $data;
        file_put_contents($this->filePath, json_encode($users, JSON_PRETTY_PRINT));
    }

    private function readData() {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $json = file_get_contents($this->filePath);
        return json_decode($json, true);
    }
}
