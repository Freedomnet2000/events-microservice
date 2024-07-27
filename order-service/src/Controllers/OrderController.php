<?php

namespace App\Controllers;

use App\Services\OrderService;

class OrderController {
    private $orderService;

    public function __construct() {
        $this->orderService = new OrderService();
    }

    public function handleRequest() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                if (isset($data['action']) && $data['action'] === 'cancelOrder') {
                    $actionResult = $this->orderService->cancelOrder($data);                 
                    echo json_encode($actionResult);
                } else {
                    $actionResult = $this->orderService->createOrder($data);
                    echo json_encode($actionResult);
                }
                break;
        }
    }
}