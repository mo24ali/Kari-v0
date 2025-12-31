<?php   

    namespace App\Entities\Roles;
    class Utilisateur{
        protected string $firstname;
        protected string $lastname;
        protected string $email;

        
        public function __construct(string $fn, string $ln, string $mail)
        {
            $this->firstname = $fn;
            $this->lastname = $ln;
            $this->email = $mail;
        }


        //getters

        public function get_email()
        {
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

        public function __toString():string
        {
             return "Hello my name is  $this->firstname $this->lastname ";
        }

    }   