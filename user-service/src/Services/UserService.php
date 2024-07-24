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

    public function createUser($data) {
        $this->userRepository->save($data);
        $this->eventPublisher->publish('UserCreated', $data);
    }
}
