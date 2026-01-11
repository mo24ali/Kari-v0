<?php

namespace App\Core;

use function App\dump_die;

class Router
{
    private array $routes = [];


    // lets pretend these are setters to the routes array so when use the 
    // resolve method we will actrually find the must be callable functions 
    // otherwise this code should return a 404 notfound status error
    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function resolve()
    {
        $method = $_SERVER['REQUEST_METHOD'];//get the servers requesy method post/get
       // parse the url to get the clean path  
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        // dump_die($path); // hey there am dump&die fucntion use me to dump outputs, you can find me in the public/index.php
        //handler contains the requested action , see uses in the KariApp file
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler) {
            $handler();
        } else {
            $this->handleNotFound();
        }
    }

    private function handleNotFound()
    {
        http_response_code(404);
        echo "Yet to come, stay tuned for more improvement ;)";
    }
}
