<?php

namespace App\Core;

class Router
{
    private array $routes = [];

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
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler) {
            call_user_func($handler);
        } else {
            $this->handleNotFound();
        }
    }

    private function handleNotFound()
    {
        http_response_code(404);
        echo "404 Not Found";
    }
}
