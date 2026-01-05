<?php

namespace App\services;

use App\core\Database;
use DateRangeError;
use PDO;
class SignUpService
{


    private ?PDO $db;



    public function __construct(PDO $connection)
    {   
            $this->db = $connection;
    }


    public function register($firstname,$lastname, $email,$password){
        $hashedPassword = password_hash($password,PASSWORD_DEFAULT);

        $sql = 'INSERT INTO users(firstname,lastname,email,password) 
                VALUES(?,?,?,?)';
        $stmt = $this->db->prepare($sql);
        if($stmt->execute([
            $firstname,
            $lastname,
            $email,
            $hashedPassword
        ])){

        }

    }
}
