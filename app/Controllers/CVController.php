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
            'mockCv' => $this->mockCvData(),
        ]);
    }

    private function mockCvData(): array
    {
        return [
            'category' => 'Software Development',
            'full_name' => 'Dang Ngoc Linh',
            'headline' => 'Frontend Developer',
            'date_of_birth' => '26/05/1998',
            'gender' => 'Female',
            'email' => 'linh.frontend@gmail.com',
            'phone_number' => '(024) 6680 5588',
            'country' => 'Vietnam',
            'city' => 'Ho Chi Minh City',
            'district' => 'District 1',
            'street_address' => '12 Nguyen Hue Street',
            'postal_code' => '700000',
            'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=400&q=80',
            'summary' => 'Frontend Developer with 4 years of experience building accessible, responsive web interfaces for SaaS products and career platforms. Strong in UI implementation, reusable components, and translating structured CV data into polished digital experiences.',
            'educations' => [
                [
                    'institution' => 'FPT University',
                    'degree_level' => 'Bachelor',
                    'major' => 'Software Engineering',
                    'start_year' => '2016',
                    'end_year' => '2020',
                    'description' => 'Graduated with distinction. Focused on web application development, database design, and user interface engineering.',
                ],
                [
                    'institution' => 'Posts & Telecommunications Institute of Technology',
                    'degree_level' => 'Certificate Program',
                    'major' => 'UX/UI Design',
                    'start_year' => '2021',
                    'end_year' => '2021',
                    'description' => 'Completed intensive coursework in design systems, layout hierarchy, and usability testing.',
                ],
            ],
            'work_histories' => [
                [
                    'job_title' => 'Frontend Developer',
                    'employment_type' => 'Full-time',
                    'industry' => 'Information Technology',
                    'company_name' => 'OneCV Labs',
                    'start_year' => '2022',
                    'end_year' => null,
                    'is_current' => true,
                    'job_description' => 'Built reusable PHP MVC views, responsive CV templates, and role-based dashboard screens. Improved form usability, implemented dynamic preview flows, and collaborated with backend developers to map form data into structured CV output.',
                ],
                [
                    'job_title' => 'Web Developer',
                    'employment_type' => 'Part-time',
                    'industry' => 'Education',
                    'company_name' => 'Bright Path Academy',
                    'start_year' => '2020',
                    'end_year' => '2022',
                    'is_current' => false,
                    'job_description' => 'Maintained course landing pages, created admin data-entry forms, and optimized frontend performance for high-traffic enrollment campaigns.',
                ],
            ],
            'certificates' => [
                [
                    'certificate_name' => 'Google UX Design Certificate',
                    'issuing_organization' => 'Google',
                    'year_issued' => '2023',
                    'description' => 'Covered user research, wireframing, prototyping, and usability testing for digital products.',
                ],
                [
                    'certificate_name' => 'Responsive Web Design',
                    'issuing_organization' => 'freeCodeCamp',
                    'year_issued' => '2022',
                    'description' => 'Validated responsive layout skills using semantic HTML, CSS Grid, Flexbox, and accessible design patterns.',
                ],
            ],
            'skills' => [
                ['skill' => 'HTML/CSS', 'proficiency' => 'Expert', 'level' => 9],
                ['skill' => 'JavaScript', 'proficiency' => 'Advanced', 'level' => 8],
                ['skill' => 'PHP MVC', 'proficiency' => 'Advanced', 'level' => 8],
                ['skill' => 'Database Design', 'proficiency' => 'Intermediate', 'level' => 7],
                ['skill' => 'UI Implementation', 'proficiency' => 'Expert', 'level' => 9],
            ],
        ];
    }

    private function requireJobSeeker(): void
    {
        if (($_SESSION['user']['role'] ?? null) !== 'job_seeker') {
            $this->redirect('/login');
        }
    }
}
