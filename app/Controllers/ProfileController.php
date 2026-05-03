<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class ProfileController extends Controller
{
    public function show(): void
    {
        if (! isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        $this->view('profile/show', [
            'title' => 'Profile',
            'user' => $_SESSION['user'],
        ]);
    }
}
