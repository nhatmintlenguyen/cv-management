<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . View::url($path));
        exit;
    }

    protected function back(): void
    {
        $fallback = View::url('/');
        $location = $_SERVER['HTTP_REFERER'] ?? $fallback;

        header('Location: ' . $location);
        exit;
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function only(array $keys): array
    {
        $data = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $_POST)) {
                $data[$key] = is_string($_POST[$key]) ? trim($_POST[$key]) : $_POST[$key];
            }
        }

        return $data;
    }

    protected function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    protected function old(array $data): void
    {
        $_SESSION['_old'] = $data;
    }
}
