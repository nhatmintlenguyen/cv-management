<?php

use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\CVController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\ProfileController;
use App\Controllers\SearchController;
use App\Core\View;

$router->get('/', [HomeController::class, 'index']);
$router->get('/home', [HomeController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);

$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/dashboard', [DashboardController::class, 'index']);

$router->get('/cv/create', [CVController::class, 'create']);
$router->get('/cv/edit', [CVController::class, 'edit']);
$router->get('/cv/edit/personal-info', [CVController::class, 'editPersonalInformation']);
$router->get('/cv/edit/academic', [CVController::class, 'editAcademic']);
$router->get('/cv/edit/qualifications', [CVController::class, 'editQualifications']);
$router->post('/cv/identity', [CVController::class, 'saveIdentity']);
$router->post('/cv/academic', [CVController::class, 'saveAcademic']);
$router->post('/cv/qualifications', [CVController::class, 'saveQualifications']);
$router->get('/cv/show', [CVController::class, 'show']);
$router->get('/cv/templates', [CVController::class, 'templates']);
$router->get('/profile', [ProfileController::class, 'show']);
$router->get('/profiles', [ProfileController::class, 'show']);
$router->get('/find-cvs', [SearchController::class, 'index']);

$router->get('/admin', function (): void {
    header('Location: ' . View::url('/admin/overview'));
    exit;
});
$router->get('/admin/overview', [AdminController::class, 'overview']);
$router->get('/admin/reference-management', [AdminController::class, 'referenceManagement']);
$router->get('/admin/reference', [AdminController::class, 'referenceManagement']);
$router->post('/admin/reference/store', [AdminController::class, 'storeReference']);
$router->post('/admin/reference/update', [AdminController::class, 'updateReference']);
$router->post('/admin/reference/delete', [AdminController::class, 'deleteReference']);
$router->get('/admin/user-management/user', [AdminController::class, 'userManagement']);
