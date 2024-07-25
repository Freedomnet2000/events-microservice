<?php

namespace App\Repositories;

class OrderRepository {
    private $conn;

    public function __construct() {
        $host = 'dpg-cqh16bdds78s73atcddg-a.frankfurt-postgres.render.com';
        $dbname = 'mydb_tjdr';
        $user = 'nir';
        $password = 'MIrQslZ0sz6u4cfrxTy0xwtFY3d1xR5x';

        $connString = "host=$host dbname=$dbname user=$user password=$password";

        $this->conn = pg_connect($connString);

        if (!$this->conn) {
            die("Could not connect to the database");
        }

        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists() {
        $query = "
            CREATE TABLE IF NOT EXISTS orders (
                id SERIAL PRIMARY KEY,
                data JSON NOT NULL,
                status VARCHAR(50) DEFAULT 'active'
            )
        ";
        pg_query($this->conn, $query);
    }

    public function save($data) {
        $query = "
            INSERT INTO orders (data, status) 
            VALUES ($1, $2)
            RETURNING id
        ";
        $result = pg_query_params($this->conn, $query, [json_encode($data), 'active']);

        if (!$result) {
            echo "Error saving order: " . pg_last_error($this->conn);
        } else {
            $row = pg_fetch_assoc($result);
            return $row['id'];
        }
    }

    public function getOrders() {
        $query = "SELECT id, data, status FROM orders";
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
        $query = "SELECT id, data, status FROM orders WHERE id = $1";
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

    public function updateOrderStatus($id, $status) {
        $query = "UPDATE orders SET status = $1 WHERE id = $2";
        $result = pg_query_params($this->conn, $query, [$status, $id]);

        if (!$result) {
            echo "Error updating order status: " . pg_last_error($this->conn);
        }
    }

    public function getOrdersByStatus($status) {
        $query = "SELECT id, data, status FROM orders WHERE status = $1";
        $result = pg_query_params($this->conn, $query, [$status]);

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
}
