<?php

namespace App\Utils;

class EventListener {
    public function processEvent($event, &$error) {
        $error = '';
        switch ($event['type']) {
            case 'UserCreated':
                // Code to handle user creation event
                break;
            case 'OrderPlaced':
                // Code to handle order placement event
                break;
            case 'OrderCancelled':
                // Code to handle order cancellation event
                break;
        }
        return true;
    }
}
