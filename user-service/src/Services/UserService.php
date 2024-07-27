<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Utils\EventPublisher;

class UserService {
    private $userRepository;
    private $eventPublisher;

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->eventPublisher = new EventPublisher();
    }

    public function createUser($email, $password, &$error) {
        $actionResult = $this->userRepository->save($email, $password, $error);
        if ($actionResult) {
            $this->eventPublisher->publish('UserCreated', ['email ' => $email,'user_id' => $actionResult['userId']]);
            return $actionResult;
        } else {
            return false;
        }
    }

    public function getUsers() {
        return $this->userRepository->getUsers();
    }
    public function authenticateUser($email, $password) {
        return $this->userRepository->verifyPassword($email, $password);
    }
}
