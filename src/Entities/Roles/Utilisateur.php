<?php

namespace App\Entities\Roles;

use App\Repositories\Impl\UserRepository;

abstract class Utilisateur

{
        protected string $firstname;
        protected string $lastname;
        protected string $email;
        protected string $role;
        protected string $phone;
        protected string $password;
        protected array $permissions = [];


        public function __construct(string $fn, string $ln, string $mail, $phone, $password)
        {
                $this->firstname = $fn;
                $this->lastname = $ln;
                $this->email = $mail;
                $this->phone = $phone;
                $this->password = $password;
        }
        public function getFirstname(): string
        {
                return $this->firstname;
        }
        public function getLastname(): string
        {
                return $this->lastname;
        }
        public function getEmail(): string
        {
                return $this->email;
        }
        public function getPassword(): string
        {
                return $this->password;
        }
        public function setPassword($password)
        {
                $this->password = $password;
        }
        public function getPhone(): string
        {
                return $this->phone;
        }
        public function __toString(): string
        {
                return "Hello my name is $this->firstname $this->lastname";
        }
}
