<?php

declare(strict_types=1);

session_start();

define('BASE_PATH', dirname(__DIR__));

$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('APP_BASE_PATH', $scriptName === '/' ? '' : rtrim($scriptName, '/'));

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';

    if (! str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $path = BASE_PATH . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($path)) {
        require $path;
    }
});

$router = new App\Core\Router();

require BASE_PATH . '/routes/web.php';

(new App\Core\App($router))->run();

unset($_SESSION['_flash'], $_SESSION['_old']);
