<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, array|callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function add(string $method, string $path, array|callable $handler): void
    {
        $this->routes[strtoupper($method)][$this->normalizePath($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $this->pathFromUri($uri);
        $handler = $this->routes[strtoupper($method)][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            View::render('errors/404', ['path' => $path]);
            return;
        }

        if (is_callable($handler)) {
            $handler();
            return;
        }

        [$controllerClass, $action] = $handler;
        $controller = new $controllerClass();
        $controller->{$action}();
    }

    private function pathFromUri(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $basePath = defined('APP_BASE_PATH') ? APP_BASE_PATH : '';

        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        return $this->normalizePath($path);
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');

        return $path === '//' ? '/' : $path;
    }
}
