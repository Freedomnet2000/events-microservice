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

        return json_decode($response, true);
    }

    public function createOrder($data) {
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['order'])) {
            return (['success' => false ,'massege' =>'Invalid input']);
        }

        $authenticatedUser = $this->getAuthenticatedUser($data['email'], $data['password']);
        if (empty($authenticatedUser['success']) || $authenticatedUser['success'] === false) {
            return $authenticatedUser;
        }
        unset($data['password']);
        $orderId = $this->orderRepository->save($data['order'],$authenticatedUser['userId']);
        $this->eventPublisher->publish('OrderPlaced', $data);
        return ['success' => true ,'message' => 'Order placed', 'Order ID' => $orderId];
    }

    public function cancelOrder($data) {
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['order_id'])) {
            return(['success' => false ,'massege' =>'Invalid input']);
        }
        $authenticatedUser = $this->getAuthenticatedUser($data['email'], $data['password']);
        if (empty($authenticatedUser['success']) || $authenticatedUser['success'] === false) return $authenticatedUser;

        $orderId = $data['order_id'];
        $userId = $authenticatedUser['userId'];
        $actionResult = $this->orderRepository->updateOrderStatus($orderId, 'cancelled',$userId);
        if ($actionResult) {
            $orderId = $actionResult['id'];
            $this->eventPublisher->publish('OrderCancelled', ['order_id' => $orderId, 'user_id' => $userId]);
            return ['success' => true , 'massege' =>"Order $orderId was cencelled successfully"];
        } else {
            return ['success' => false , 'massege' =>"No rows were updated. Please check the ID and user datails"];
        }
    }
}

