<?php

namespace App\Controllers;

use App\Services\UserService;

class UserController {
    private $userService;

    public function __construct() {
        $this->userService = new UserService();
    }

    public function handleRequest() {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $this->userService->createUser($data);
                echo json_encode(['message' => 'User created']);
                break;
            case 'GET':
                $users = $this->userService->getUsers();
                echo json_encode($users);
                break;
        }
    }
}
