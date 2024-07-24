<?php

namespace App\Utils;

class EventListener {
    public function processEvent($event) {
        switch ($event['type']) {
            case 'UserCreated':
                // קוד לטיפול באירוע יצירת משתמש
                break;
            case 'OrderPlaced':
                // קוד לטיפול באירוע הנחת הזמנה
                break;
            case 'OrderCancelled':
                // קוד לטיפול באירוע ביטול הזמנה
                break;
        }
    }
}
