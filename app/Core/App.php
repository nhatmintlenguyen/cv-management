<?php

declare(strict_types=1);

namespace App\Core;

class App
{
    public function __construct(private Router $router)
    {
    }

    public function run(): void
    {
        $this->router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    }
}
