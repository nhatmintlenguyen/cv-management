<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index', [
            'title' => 'Homepage',
            'metaDescription' => 'Create professional CVs with structured forms, choose polished templates, and explore role-based tools for job seekers and employers on OneCV.',
            'canonicalPath' => '/',
        ]);
    }
}
