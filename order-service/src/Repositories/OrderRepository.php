<?php

namespace App\Repositories;

class OrderRepository {
    private $filePath;

    public function __construct() {
        $this->filePath = __DIR__ . '/../../data/orders.json';
    }

    public function save($data) {
        $orders = $this->readData();
        $orders[] = $data;
        file_put_contents($this->filePath, json_encode($orders, JSON_PRETTY_PRINT));
    }

    public function getOrders() {
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
