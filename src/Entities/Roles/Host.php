<?php

namespace App\Entities\Roles;



class Host extends Utilisateur
{

    public function __construct(
        string $email,
        string $firstname,
        string $lastname,
        string $phone,
        string $passwd
    ) {
        parent::__construct(
            $firstname,
            $lastname,
            $email,
            $phone,
            $passwd
        );
        $this->role = 'host';
    }
}
