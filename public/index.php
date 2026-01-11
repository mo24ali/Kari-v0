<?php

namespace App;
use App\KariApp;

const BASE_PATH = __DIR__ . '/../';

require_once BASE_PATH . 'vendor/autoload.php';
// require_once BASE_PATH . 'src/config/connexion.php';

KariApp::run();

function dump_die($value)
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die();
}
