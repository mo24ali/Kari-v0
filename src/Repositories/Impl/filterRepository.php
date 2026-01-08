<?php

use App\core\Database;

    class filterRepository implements filterInterface{



        private ?PDO $db;



        public function __construct()
        {
            $this->db = Database::getInstance()->getConnection();
        }
        public function findByCity(string $city): bool|array
        {
            $rquest = "select * from logement where city=?";
            $stmt = $this->db->prepare($rquest);
            return $stmt->execute([$city]);
        }


        public function findByDate(string $startDate, string $endDate): bool|array
        {
            $rquest = "select * from logement where ";
            $stmt = $this->db->prepare($rquest);
            return $stmt->execute([]);
        }

        public function findByVoyageur(int $nb): bool|array
        {
            return true;
        }

    }