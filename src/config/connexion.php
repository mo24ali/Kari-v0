<?php

namespace App\config;

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../'); 
$dotenv->load();

return [
    'host'     => 'localhost',
    'dbname'   => 'kari',
    'username' => $_ENV['DB_USERNAME'] , 
    'password' => $_ENV['DB_PASSWD'] ,
    'charset'  => 'utf8'
];