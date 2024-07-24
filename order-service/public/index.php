<?php

require_once '../vendor/autoload.php';

use App\Controllers\OrderController;

$controller = new OrderController();
$controller->handleRequest();
