<?php

require_once '../vendor/autoload.php';

use App\Controllers\UserController;

$controller = new UserController();
$controller->handleRequest();