<?php


namespace App\Entities\Models;

class Reservation
{
    private int $id_log;
    private int $id_user;

    public function __construct(int $id_user, int $id_log)
    {
        $this->id_user = $id_user;
        $this->id_log = $id_log;
    }
}
