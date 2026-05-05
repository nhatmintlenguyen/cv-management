<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/main'): void
    {
        $viewPath = dirname(__DIR__, 2) . '/resources/views/' . str_replace('.', '/', $view) . '.php';

        if (! file_exists($viewPath)) {
            http_response_code(500);
            echo "View not found: {$view}";
            return;
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutPath = dirname(__DIR__, 2) . '/resources/views/' . str_replace('.', '/', $layout) . '.php';

        if (! file_exists($layoutPath)) {
            echo $content;
            return;
        }

        require $layoutPath;
    }

    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public static function url(string $path = '/'): string
    {
        $basePath = defined('APP_BASE_PATH') ? APP_BASE_PATH : '';
        $path = '/' . ltrim($path, '/');

        return $basePath . $path;
    }

    public static function asset(string $path): string
    {
        $path = ltrim($path, '/');
        $publicRoot = dirname(__DIR__, 2) . '/public/';
        $publicPath = $publicRoot . $path;
        $urlPath = $path;

        if (! file_exists($publicPath)) {
            $publicPath = $publicRoot . 'assets/' . $path;
            $urlPath = 'assets/' . $path;
        }

        $version = file_exists($publicPath) ? '?v=' . filemtime($publicPath) : '';

        return self::url('/' . $urlPath) . $version;
    }
}
