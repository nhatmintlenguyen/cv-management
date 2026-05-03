<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class SearchController extends Controller
{
    public function index(): void
    {
        $this->requireEmployer();

        $this->view('search/index', [
            'title' => 'Find CVs',
            'user' => $_SESSION['user'],
        ]);
    }

    private function requireEmployer(): void
    {
        if (($_SESSION['user']['role'] ?? null) !== 'employer') {
            $this->redirect('/login');
        }
    }
}
