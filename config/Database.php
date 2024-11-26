<?php

namespace Config;

use PDO;
class Database{
    private static $instance = null;
    private $connection;

    private function __construct() {
        $host = $_ENV['DB_HOST'];
        $db_name = $_ENV['DB_DATABASE'];
        $user = $_ENV['DB_USERNAME'];
        $pass = $_ENV['DB_PASSWORD'];
        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$db_name", $user,$pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection error: ' . $e->getMessage();
        }
    }

    public static function get_instance():Database{
        if(self::$instance === null)
            self::$instance = new self();
        return self::$instance;
    }

    public function get_connection():PDO{
        return $this->connection;
    }
}