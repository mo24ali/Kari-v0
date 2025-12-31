<?php

namespace App\core;

use App\config;
use PDO;
use PDOException;

class Database
{
    public static ?PDO $conn;
    public static ?Database $instance;

    private function __construct()
    {

        $config = require_once __DIR__ . "../config/connexion.php";
        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']}";
            self::$conn = new PDO($dsn, $config['username'], $config['password']);
            echo "established";
        } catch (PDOException $pe) {
            die($pe->getMessage());
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    public function getConnection()
    {
        return self::$conn;
    }
}
