<?php

namespace App\Entities\Roles;

// use App\core\Database;
use App\Repository\UseRepository;
use Exception;

class Utilisateur implements UseRepository

{
        protected string $firstname;
        protected string $lastname;
        protected string $email;

        // protected Database $db;

        public function __construct(string $fn, string $ln, string $mail)
        {
                $this->firstname = $fn;
                $this->lastname = $ln;
                $this->email = $mail;
                // $this->db = Database::getInstance();
        }


        //getters

        public function get_email()
        {

                // $this->db->getConnection();
                return $this->email;
        }
        public function get_firstname()
        {
                return $this->firstname;
        }
        public function get_lastname()
        {
                return $this->lastname;
        }
        //setters
        public function set_email(string $email)
        {
                $this->email = $email;
        }
        public function set_firstname(string $firstname)
        {
                $this->firstname = $firstname;
        }
        public function set_lastname(string $lastname)
        {
                $this->lastname  = $lastname;
        }

        public function __toString(): string
        {
                return "Hello my name is  $this->firstname $this->lastname ";
        }

        public function findById()
        {
                try{
                        
                }catch(Exception $e){
                        die($e->getMessage());
                }
        }
}
