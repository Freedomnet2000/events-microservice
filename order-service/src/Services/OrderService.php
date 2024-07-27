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

    private function getAuthenticatedUser($email, $password) {
        $fileds = ['email' => $email, 'password' => $password ,'action'=>'authenticate'];
        $ch = curl_init('http://localhost:8001/index.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fileds));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        curl_close($ch);

        $responseDecoded = json_decode($response, true);
        
        if ($responseDecoded["message"] !== 'User authenticated') {
           die('Authentication failed');
        }

        return $responseDecoded;
    }

    public function createOrder($data) {
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['order'])) {
            die('Invalid input');
        }

        $authenticatedUser = $this->getAuthenticatedUser($data['email'], $data['password']);
        unset($data['password']);
        $orderId = $this->orderRepository->save($data['order'],$authenticatedUser['userId']);
        $this->eventPublisher->publish('OrderPlaced', $data);
        return $orderId;
    }

    public function cancelOrder($data) {
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['order_id'])) {
            die('Invalid input');
        }
        $authenticatedUser = $this->getAuthenticatedUser($data['email'], $data['password']);
        $orderId = $data['order_id'];
        $userId = $authenticatedUser['userId'];
        $actionResult = $this->orderRepository->updateOrderStatus($orderId, 'cancelled',$userId);
        $this->eventPublisher->publish('OrderCancelled', ['id' => $orderId, 'actionResult' => $actionResult]);
        return $actionResult;
    }
}

