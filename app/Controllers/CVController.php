<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\CV;
use App\Models\CVCategory;
use App\Models\District;
use App\Models\Gender;
use Throwable;

class CVController extends Controller
{
    public function create(): void
    {
        $this->requireJobSeeker();
        $this->view('cv/create', $this->builderStepOneViewData('Create CV'));
    }

    public function edit(): void
    {
        $this->requireJobSeeker();
        $this->view('cv/edit', $this->builderStepOneViewData('CV Builder'));
    }

    public function saveIdentity(): void
    {
        $this->requireJobSeeker();

        $data = $this->only([
            'full_name',
            'date_of_birth',
            'gender_id',
            'email',
            'phone_number',
            'country_id',
            'city_id',
            'district_id',
            'street_address',
            'postal_code',
            'cv_category_id',
            'summary',
        ]);

        $errors = $this->validateIdentity($data);

        if ($errors !== []) {
            $this->flash('errors', $errors);
            $this->old($data);
            $this->redirect('/cv/edit');
        }

        $payload = [
            'cv_category_id' => (int) $data['cv_category_id'],
            'gender_id' => (int) $data['gender_id'],
            'country_id' => (int) $data['country_id'],
            'city_id' => (int) $data['city_id'],
            'district_id' => ($data['district_id'] ?? '') === '' ? null : (int) $data['district_id'],
            'full_name' => trim((string) $data['full_name']),
            'date_of_birth' => $data['date_of_birth'],
            'email' => strtolower(trim((string) $data['email'])),
            'phone_number' => trim((string) $data['phone_number']),
            'street_address' => trim((string) $data['street_address']),
            'postal_code' => trim((string) ($data['postal_code'] ?? '')) ?: null,
            'summary' => trim((string) ($data['summary'] ?? '')) ?: null,
        ];

        $cvModel = new CV();
        $userId = (int) $_SESSION['user']['id'];

        if ($cvModel->findByUserId($userId) === null) {
            $cvModel->createForUser($userId, $payload);
        } else {
            $cvModel->updateForUser($userId, $payload);
        }

        $this->flash('success', 'Your personal information has been saved.');
        $this->redirect('/cv/edit');
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

    private function builderStepOneViewData(string $title): array
    {
        $cv = (new CV())->findByUserId((int) ($_SESSION['user']['id'] ?? 0));

        return [
            'title' => $title,
            'cv' => $cv,
            'genders' => $this->safeLookup(fn (): array => (new Gender())->all('name')),
            'countries' => $this->safeLookup(fn (): array => (new Country())->all('name')),
            'cities' => $this->safeLookup(fn (): array => (new City())->all('name')),
            'districts' => $this->safeLookup(fn (): array => (new District())->all('name')),
            'categories' => $this->safeLookup(fn (): array => (new CVCategory())->all('name')),
        ];
    }

    private function safeLookup(callable $loader): array
    {
        try {
            return $loader();
        } catch (Throwable) {
            return [];
        }
    }

    private function validateIdentity(array $data): array
    {
        $errors = [];

        if (trim((string) ($data['full_name'] ?? '')) === '') {
            $errors[] = 'Full name is required.';
        }

        if (empty($data['date_of_birth']) || strtotime((string) $data['date_of_birth']) === false) {
            $errors[] = 'A valid date of birth is required.';
        }

        if (empty($data['email']) || ! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }

        if (trim((string) ($data['phone_number'] ?? '')) === '') {
            $errors[] = 'Phone number is required.';
        }

        if (trim((string) ($data['street_address'] ?? '')) === '') {
            $errors[] = 'Street address is required.';
        }

        $genderId = (int) ($data['gender_id'] ?? 0);
        $countryId = (int) ($data['country_id'] ?? 0);
        $cityId = (int) ($data['city_id'] ?? 0);
        $districtId = (int) ($data['district_id'] ?? 0);
        $categoryId = (int) ($data['cv_category_id'] ?? 0);

        if ($genderId <= 0 || ! (new Gender())->exists($genderId)) {
            $errors[] = 'Please choose a valid gender.';
        }

        if ($countryId <= 0 || ! (new Country())->exists($countryId)) {
            $errors[] = 'Please choose a valid country.';
        }

        $city = $cityId > 0 ? (new City())->find($cityId) : null;
        if ($city === null || (int) $city['country_id'] !== $countryId) {
            $errors[] = 'Please choose a valid city/province for the selected country.';
        }

        if ($districtId > 0) {
            $district = (new District())->find($districtId);
            if ($district === null || (int) $district['city_id'] !== $cityId) {
                $errors[] = 'Please choose a valid district for the selected city.';
            }
        }

        if ($categoryId <= 0 || ! (new CVCategory())->exists($categoryId)) {
            $errors[] = 'Please choose a valid CV category.';
        }

        return $errors;
    }

    private function requireJobSeeker(): void
    {
        if (($_SESSION['user']['role'] ?? null) !== 'job_seeker') {
            $this->redirect('/login');
        }
    }
}
