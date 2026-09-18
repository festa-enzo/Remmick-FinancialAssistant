<?php

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();

        $host     = $_ENV['DB_HOST'];
        $port     = $_ENV['DB_PORT'];
        $dbname   = $_ENV['DB_DATABASE'];
        $user     = $_ENV['DB_USERNAME'];
        $pass     = $_ENV['DB_PASSWORD'];

        try {
            $this->pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
                $user, 
                $pass
            );
            
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erro de conexão com o banco: " . $e->getMessage());
        }
    }

    public static function getConnection() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}