<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Utils\EventPublisher;

class OrderService {
    private $orderRepository;
    private $eventPublisher;

    public function __construct() {
        $this->orderRepository = new OrderRepository();
        $this->eventPublisher = new EventPublisher();
    }

    public function createOrder($data) {
        $this->orderRepository->save($data);
        $this->eventPublisher->publish('OrderPlaced', $data);
    }

    // public function cancelOrder($orderId) {
    //     $this->orderRepository->delete($orderId);
    //     $this->eventPublisher->publish('OrderCancelled', ['id' => $orderId]);
    // }
}
