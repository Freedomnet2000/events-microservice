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

    private function getAuthenticatedUser($email, $password, &$error) {
        $fileds = ['email' => $email, 'password' => $password ,'action'=>'authenticate'];
        $ch = curl_init('http://localhost:8001/index.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fileds));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        // echo $response;
        // exit();
        // return;
        curl_close($ch);
        if (!$response) {
            $error = 'Could not authenticate';
            return false;
        }

        $response = json_decode($response, true);
        if (!$response['success']) {
            $error = $response['message'];
            return false;
        }
        return $response;
    }

    public function createOrder($data, &$error) {
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['order'])) {
            $error = 'Invalid input';
            return false;
        }

        $authenticatedUser = $this->getAuthenticatedUser($data['email'], $data['password'], $error);
        if (!$authenticatedUser) {
            return false;
        }
        unset($data['password']);
        $orderId = $this->orderRepository->save($data['order'],$authenticatedUser['userId']);
        $this->eventPublisher->publish('OrderPlaced', $data);
        return $orderId;
    }

    public function cancelOrder($data, &$error) {
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['order_id'])) {
            $error = 'Invalid input';
            return false;
        }

        $authenticatedUser = $this->getAuthenticatedUser($data['email'], $data['password'], $error);
        if (!$authenticatedUser) {
            return false;
        }

        $orderId = $data['order_id'];
        $userId = $authenticatedUser['userId'];
        $actionResult = $this->orderRepository->updateOrderStatus($orderId, 'cancelled', $userId, $error);
        if ($actionResult) {
            $orderId = $actionResult['id'];
            $this->eventPublisher->publish('OrderCancelled', ['order_id' => $orderId, 'user_id' => $userId]);
            return true;
        } else {
            $error = 'No rows were updated. Please check the ID and user datails';
            return false;
        }
    }
}

