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
                if (isset($data['action']) && $data['action'] === 'authenticate') {
                    $this->authenticateUser($data);
                } else {
                    $this->createUser($data);
                }
                break;
            case 'GET':
                $users = $this->userService->getUsers();
                echo json_encode($users);
                break;
        }
    }

    private function createUser($data) {
        $error = '';
        if (isset($data['email'], $data['password'])) {
            if (strlen($data['password']) < 6 ) {
                echo json_encode(['success' => false ,'message' => 'password should be 5 charecters minimun']);
                return;
            }
            $actionResult = $this->userService->createUser($data['email'], $data['password'], $error);
            if (!$actionResult) {
                echo json_encode(['success' => false ,'message' => $error]);}
                else {
                    echo json_encode(['success' => true ,'message' => 'User created. User ID :'.$actionResult['userId']]);
                }
        } else {
            echo json_encode(['success' => false ,'message' => 'Invalid input'], JSON_THROW_ON_ERROR, 400);
        }
    }

    private function authenticateUser($data) {
        if (isset($data['email'], $data['password'])) {
            $error = '';
            $authenticatedUser = $this->userService->authenticateUser($data['email'], $data['password']);
            if ($authenticatedUser) {
                echo json_encode(['success' => true,'message' => 'User authenticated','userId' =>$authenticatedUser['id']]);
            } else {
                echo json_encode(['success' => false ,'message' => 'user authentication failed'], JSON_THROW_ON_ERROR, 401);
            }
        } else {
            echo json_encode(['success' => false ,'message' => 'Invalid input'], JSON_THROW_ON_ERROR, 400);
        }
    }
}
