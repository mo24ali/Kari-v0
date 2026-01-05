<?php

namespace App\core;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private static ?PDO $conn = null;

    private function __construct()
    {

        $config = require __DIR__ . '/../config/connexion.php';
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']}";
            self::$conn = new PDO($dsn, $config['username'], $config['password']);

            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $pe) {
            die("Connection Error: " . $pe->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return self::$conn;
    }
}
