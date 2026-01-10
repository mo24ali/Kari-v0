<?php

namespace App\Entities\Models;

class Favoris
{
    private int $user_id;
    private int $logement_id;

    public function __construct(
        int $user_id,
        int $logement_id
    ) {
        $this->user_id = $user_id;
        $this->logement_id = $logement_id;
    }

}
