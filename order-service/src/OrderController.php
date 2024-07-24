<?php

namespace App\Controllers;

use App\Services\OrderService;

class OrderController {
    private $orderService;

    public function __construct() {
        $this->orderService = new OrderService();
    }

    public function handleRequest() {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $this->orderService->createOrder($data);
                echo json_encode(['message' => 'Order placed']);
                break;
            case 'DELETE':
                $orderId = $_GET['id'];
                $this->orderService->cancelOrder($orderId);
                echo json_encode(['message' => 'Order cancelled']);
                break;
        }
    }
}
