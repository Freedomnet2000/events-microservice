<?php

namespace App\Controllers;

use App\Services\OrderService;

class OrderController {
    private $orderService;

    public function __construct() {
        $this->orderService = new OrderService();
    }

    public function handleRequest() {
        $error = '';
        $data = json_decode(file_get_contents('php://input'), true);
        
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                if (isset($data['action']) && $data['action'] === 'cancelOrder') {
                    $actionResult = $this->orderService->cancelOrder($data, $error);                 

                    if (!$actionResult) {
                        echo json_encode(['success' => false, 'message' => $error]);
                    } else {
                        echo json_encode(['success' => true, 'orderId' => $data['order_id']]);
                    }
                } else {
                    $actionResult = $this->orderService->createOrder($data, $error);

                    if (!$actionResult) {
                        echo json_encode(['success' => false, 'message' => $error]);
                    } else {
                        echo json_encode(['success' => true, 'orderId' => $actionResult]);
                    }
                }
                break;
        }
    }
}