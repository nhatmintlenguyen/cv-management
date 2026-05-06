<?php

use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\CVController;
use App\Controllers\DashboardController;
use App\Controllers\HomeController;
use App\Controllers\JobSearchController;
use App\Controllers\JobVacancyController;
use App\Controllers\ProfileController;
use App\Controllers\ReferenceApiController;
use App\Controllers\SearchController;
use App\Core\View;

$router->get('/', [HomeController::class, 'index']);
$router->get('/home', [HomeController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
$router->post('/forgot-password', [AuthController::class, 'sendResetLink']);
$router->get('/reset-password', [AuthController::class, 'showResetPassword']);
$router->post('/reset-password', [AuthController::class, 'resetPassword']);

$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);

$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/api/references', [ReferenceApiController::class, 'index']);

$router->get('/cv/create', [CVController::class, 'create']);
$router->get('/cv/edit', [CVController::class, 'edit']);
$router->get('/cv/edit/personal-info', [CVController::class, 'editPersonalInformation']);
$router->get('/cv/edit/academic', [CVController::class, 'editAcademic']);
$router->get('/cv/edit/qualifications', [CVController::class, 'editQualifications']);
$router->get('/cv/edit/review', [CVController::class, 'editReview']);
$router->post('/cv/identity', [CVController::class, 'saveIdentity']);
$router->post('/cv/academic', [CVController::class, 'saveAcademic']);
$router->post('/cv/qualifications', [CVController::class, 'saveQualifications']);
$router->post('/cv/finish', [CVController::class, 'finish']);
$router->get('/cv/show', [CVController::class, 'show']);
$router->get('/cv/templates', [CVController::class, 'templates']);
$router->get('/profile', [ProfileController::class, 'show']);
$router->post('/profile', [ProfileController::class, 'update']);
$router->get('/profiles', [ProfileController::class, 'show']);
$router->get('/find-cvs', [SearchController::class, 'index']);
$router->get('/find-cvs/show', [SearchController::class, 'show']);
$router->get('/jobs', [JobSearchController::class, 'index']);
$router->get('/jobs/show', [JobSearchController::class, 'show']);

$router->get('/employer/jobs', [JobVacancyController::class, 'index']);
$router->get('/employer/jobs/edit', [JobVacancyController::class, 'edit']);
$router->post('/employer/jobs/delete', [JobVacancyController::class, 'delete']);
$router->post('/employer/jobs/toggle-status', [JobVacancyController::class, 'toggleStatus']);
$router->get('/employer/jobs/create', [JobVacancyController::class, 'create']);
$router->get('/employer/jobs/create/basics', [JobVacancyController::class, 'basics']);
$router->post('/employer/jobs/create/basics', [JobVacancyController::class, 'saveBasics']);
$router->get('/employer/jobs/create/location', [JobVacancyController::class, 'location']);
$router->post('/employer/jobs/create/location', [JobVacancyController::class, 'saveLocation']);
$router->get('/employer/jobs/create/requirements', [JobVacancyController::class, 'requirements']);
$router->post('/employer/jobs/create/requirements', [JobVacancyController::class, 'saveRequirements']);
$router->get('/employer/jobs/create/review', [JobVacancyController::class, 'review']);
$router->post('/employer/jobs/create/publish', [JobVacancyController::class, 'publish']);

$router->get('/admin', function (): void {
    header('Location: ' . View::url('/admin/overview'));
    exit;
});
$router->get('/admin/overview', [AdminController::class, 'overview']);
$router->get('/admin/job-vacancies', [AdminController::class, 'jobVacancies']);
$router->post('/admin/job-vacancies/delete', [AdminController::class, 'deleteJobVacancy']);
$router->get('/admin/reference-management', [AdminController::class, 'referenceManagement']);
$router->get('/admin/reference', [AdminController::class, 'referenceManagement']);
$router->post('/admin/reference/store', [AdminController::class, 'storeReference']);
$router->post('/admin/reference/update', [AdminController::class, 'updateReference']);
$router->post('/admin/reference/delete', [AdminController::class, 'deleteReference']);
$router->get('/admin/user-management/user', [AdminController::class, 'userManagement']);
