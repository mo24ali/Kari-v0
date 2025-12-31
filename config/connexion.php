<?php


namespace App\config;
use Dotenv\Dotenv;
require_once __DIR__ ."/../.env";
    return $connection = [
        'host' => 'localhost',
        'dbname' => 'kari',
        'username' => $_ENV['db_username'],
        'password' => $_ENV['db_passwd'],
        'charset' => 'utf8'
    ];

