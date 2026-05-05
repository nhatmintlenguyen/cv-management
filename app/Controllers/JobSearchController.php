<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class JobSearchController extends Controller
{
    public function index(): void
    {
        if (($_SESSION['user']['role'] ?? null) !== 'job_seeker') {
            $this->redirect('/login');
        }

        $this->view('jobs/index', [
            'title' => 'Job Search',
        ]);
    }
}
