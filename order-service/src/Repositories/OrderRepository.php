<?php

namespace App\Repositories;

class OrderRepository {
    private $conn;

    public function __construct() {
        $configPath = __DIR__ . '/../../config/PostgresqlConnect.php';

        if (filesize($configPath) === 0) {
            die("Configuration file is empty");
        }

        $config = require $configPath;
        $host = $config['host'];
        $dbname = $config['dbname'];
        $user = $config['user'];
        $password = $config['password'];

        $connString = "host=$host dbname=$dbname user=$user password=$password";

        $this->conn = pg_connect($connString);

        if (!$this->conn) {
            die("Could not connect to the database");
        }

        $this->createTableIfNotExists();
        // $this->updateTableStructure();
    }

    private function createTableIfNotExists() {
        $query = "
            CREATE TABLE IF NOT EXISTS orders (
                id SERIAL PRIMARY KEY,
                data JSON NOT NULL,
                status VARCHAR(50) DEFAULT 'active',
                user_id INT NOT NULL
            )
        ";
        pg_query($this->conn, $query);
    }

    private function updateTableStructure() {
        $result = pg_query($this->conn, "
            SELECT column_name
            FROM information_schema.columns
            WHERE table_name='orders' AND column_name='user_id'
        ");

        if (pg_num_rows($result) == 0) {
            $query = "ALTER TABLE orders ADD COLUMN user_id INT NOT NULL";
            pg_query($this->conn, $query);
        }
    }

    public function save($data, $userId) {
        $query = "
            INSERT INTO orders (data, status, user_id) 
            VALUES ($1, $2, $3)
            RETURNING id
        ";
        $result = pg_query_params($this->conn, $query, [json_encode($data), 'active', $userId]);

        if (!$result) {
            echo "Error saving order: " . pg_last_error($this->conn);
        } else {
            $row = pg_fetch_assoc($result);
            return $row['id'];
        }
    }

    public function getOrders() {
        $query = "SELECT id, data, status, user_id FROM orders";
        $result = pg_query($this->conn, $query);

        if (!$result) {
            echo "Error fetching orders: " . pg_last_error($this->conn);
            return [];
        }

        $orders = pg_fetch_all($result);
        return array_map(function($order) {
            $order['data'] = json_decode($order['data'], true);
            return $order;
        }, $orders ? $orders : []);
    }

    public function getOrderById($id) {
        $query = "SELECT id, data, status, user_id FROM orders WHERE id = $1";
        $result = pg_query_params($this->conn, $query, [$id]);

        if (!$result) {
            echo "Error fetching order: " . pg_last_error($this->conn);
            return null;
        }

        $order = pg_fetch_assoc($result);
        if ($order) {
            $order['data'] = json_decode($order['data'], true);
        }
        return $order;
    }

    public function updateOrderStatus($id, $status, $userId, &$error) {
        $query = "UPDATE orders SET status = $1 WHERE id = $2 and user_id = $3";
        $result = pg_query_params($this->conn, $query, [$status, $id, $userId]);
        if (!$result) {
            $error = "Error updating order status: " . pg_last_error($this->conn);
            return false;
        } else {
            $affectedRows = pg_affected_rows($result);
            return $affectedRows == 0 ? false : ['id' =>$id];
        }
    }
}
