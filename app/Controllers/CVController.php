<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class CVController extends Controller
{
    public function create(): void
    {
        $this->requireJobSeeker();
        $this->view('cv/create', ['title' => 'Create CV']);
    }

    public function edit(): void
    {
        $this->requireJobSeeker();
        $this->view('cv/edit', ['title' => 'CV Builder']);
    }

    public function show(): void
    {
        $this->requireJobSeeker();
        $this->view('cv/show', ['title' => 'CV Preview']);
    }

    public function templates(): void
    {
        $this->requireJobSeeker();
        $this->view('cv/templates', ['title' => 'CV Templates']);
    }

    private function requireJobSeeker(): void
    {
        if (($_SESSION['user']['role'] ?? null) !== 'job_seeker') {
            $this->redirect('/login');
        }
    }
}
