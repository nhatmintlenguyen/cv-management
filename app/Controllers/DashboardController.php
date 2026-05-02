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

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'user' => $_SESSION['user'],
        ]);
    }
}
