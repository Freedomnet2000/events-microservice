<?php

require_once '../vendor/autoload.php';

use App\Controllers\EventController;

$controller = new EventController();
$controller->handleRequest();
