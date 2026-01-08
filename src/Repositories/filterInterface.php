<?php


    interface filterInterface{

        public function findByCity(string $city): bool|array;
        public function findByDate(string $startDate, string $endDate): bool|array;
        public function findByVoyageur(int $nb): bool|array;
    }