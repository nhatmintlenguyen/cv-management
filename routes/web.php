<?php

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Core\View;

$router->get('/', function (): void {
    header('Location: ' . View::url('/login'));
    exit;
});

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);

$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/dashboard', [DashboardController::class, 'index']);
