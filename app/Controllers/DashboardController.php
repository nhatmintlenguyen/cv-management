<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function index(): void
    {
        if (! isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if (($_SESSION['user']['role'] ?? null) === 'admin') {
            $this->redirect('/admin/overview');
        }

        if (($_SESSION['user']['role'] ?? null) === 'job_seeker') {
            $this->redirect('/cv/templates');
        }

        if (($_SESSION['user']['role'] ?? null) === 'employer') {
            $this->redirect('/find-cvs');
        }

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'user' => $_SESSION['user'],
        ]);
    }
}
