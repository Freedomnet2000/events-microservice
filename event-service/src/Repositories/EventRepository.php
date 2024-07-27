<?php

namespace App\Repositories;

class EventRepository {
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
            CREATE TABLE IF NOT EXISTS public.events (
                id SERIAL PRIMARY KEY,
                type VARCHAR(255) NOT NULL,
                data JSON NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        pg_query($this->conn, $query);
    }

    public function save($type, $data, &$error) {
        $query = "
            INSERT INTO public.events (type, data) 
            VALUES ($1, $2)
            RETURNING id
        ";
        
        $result = pg_query_params($this->conn, $query, [$type, json_encode($data)]);

        if (!$result) {
            $error = "Error saving event: " . pg_last_error($this->conn);
            return false;
        } else {
            $row = pg_fetch_assoc($result);
            return $row['id'];
        }
    }

    public function getEvents() {
        $query = "SELECT id, type, data, created_at FROM public.events";
        $result = pg_query($this->conn, $query);

        if (!$result) {
            echo "Error fetching events: " . pg_last_error($this->conn);
            return [];
        }

        $events = pg_fetch_all($result);
        return array_map(function($event) {
            $event['data'] = json_decode($event['data'], true);
            return $event;
        }, $events ? $events : []);
    }
}
