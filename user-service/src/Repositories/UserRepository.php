<?php

namespace App\Repositories;

use Error;

class UserRepository {
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
            CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL
            )
        ";
        pg_query($this->conn, $query);
    }

    public function save($email, $password, &$error) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "
            INSERT INTO users (email, password) 
            VALUES ($1, $2)
            RETURNING id
        ";
    
        $result = @pg_query_params($this->conn, $query, [$email, $hashedPassword]);
    
        if (!$result) {
            $pgError = pg_last_error($this->conn);
            if (strpos($pgError, 'duplicate key value violates unique constraint') !== false) {
                $error = 'Email already exists';
            } else {
                $error = $pgError;
            }
            return false;
        } else {
            $row = pg_fetch_assoc($result);
            return ['userId' =>$row['id']];
        }
    }

    public function getUsers() {
        $query = "SELECT id, email FROM users";
        $result = pg_query($this->conn, $query);

        if (!$result) {
            echo "Error fetching users: " . pg_last_error($this->conn);
            return [];
        }

        $users = pg_fetch_all($result);
        return $users ? $users : [];
    }

    public function getUserByEmail($email) {
        $query = "SELECT id, email, password FROM users WHERE email = $1";
        $result = pg_query_params($this->conn, $query, [$email]);

        if (!$result) {
            echo "Error fetching user: " . pg_last_error($this->conn);
            return null;
        }

        $user = pg_fetch_assoc($result);
        return $user;
    }

    public function verifyPassword($email, $password) {
        $user = $this->getUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        } else {
            return false;
        }
    }
}
