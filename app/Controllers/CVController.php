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
        $templates = [
            'modern' => [
                'name' => 'Modern Executive',
                'description' => 'Asymmetric, bold typography',
                'image' => 'https://www.topcv.vn/cv/snapshot/template-cv/mau-cv-hien-dai-1-_VAFaBAAJVw0CAQ4CUQECV1QABQcCAgkCVgNVCw391b.webp?t=1750134891&color=a94a4b&template_name=modern_1_v2&lang=vi',
                'accent' => '#a94a4b',
            ],
            'classic' => [
                'name' => 'Classic Editorial',
                'description' => 'Serif, single column focus',
                'image' => 'https://www.topcv.vn/cv/snapshot/template-cv/mau-cv-an-tuong-6-_VlJVBQcNCQgBBVdWAgMCVQFTUAYPVgBUCVNRAQb0f5.webp?t=1756435041&color=574040&template_name=impressive_6_v2&lang=vi',
                'accent' => '#574040',
            ],
            'minimal' => [
                'name' => 'Senior Minimal',
                'description' => 'Ultra-clean, high whitespace',
                'image' => 'https://www.topcv.vn/cv/snapshot/template-cv/mau-cv-senior-_B1tfBFEAUgALBAgBBl0MD1wECQBQAQFQAwIAAw44ec.webp?t=1756265781&color=000000&template_name=senior_v2&lang=vi',
                'accent' => '#000000',
            ],
        ];

        $selected = $_GET['template'] ?? 'modern';

        if (! array_key_exists($selected, $templates)) {
            $selected = 'modern';
        }

        $this->view('cv/templates', [
            'title' => 'CV Templates',
            'templates' => $templates,
            'selectedTemplate' => $selected,
        ]);
    }

    private function requireJobSeeker(): void
    {
        if (($_SESSION['user']['role'] ?? null) !== 'job_seeker') {
            $this->redirect('/login');
        }
    }
}
