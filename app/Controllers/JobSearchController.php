<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\City;
use App\Models\Country;
use App\Models\EmploymentType;
use App\Models\JobCategory;
use App\Models\JobLevel;
use App\Models\JobVacancy;
use App\Models\JobVacancySkill;
use App\Models\SalaryRange;
use App\Models\Skill;
use App\Models\WorkArrangement;
use Throwable;

class JobSearchController extends Controller
{
    private const PER_PAGE = 10;

    public function index(): void
    {
        if (! in_array(($_SESSION['user']['role'] ?? null), ['job_seeker', 'employer', 'admin'], true)) {
            $this->redirect('/login');
        }

        $filters = $this->filtersFromQuery();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $filters['limit'] = $page * self::PER_PAGE;
        $filters['offset'] = 0;
        $jobVacancies = new JobVacancy();
        $rows = $this->safeLookup(fn (): array => $jobVacancies->searchActive($filters));
        $total = $this->safeCount(fn (): int => $jobVacancies->countActive($filters));

        $data = [
            'title' => 'Job Search',
            'metaDescription' => 'Search active job vacancies on OneCV by keyword, category, location, required skills, salary, employment type, level, and work arrangement.',
            'canonicalPath' => '/jobs',
            'jobs' => $rows,
            'filters' => $filters,
            'page' => $page,
            'perPage' => self::PER_PAGE,
            'total' => $total,
            'jobCategories' => $this->safeLookup(fn (): array => (new JobCategory())->all('name')),
            'countries' => $this->safeLookup(fn (): array => (new Country())->all('name')),
            'cities' => $this->safeLookup(fn (): array => (new City())->all('name')),
            'skills' => $this->safeLookup(fn (): array => (new Skill())->all('name')),
            'employmentTypes' => $this->safeLookup(fn (): array => (new EmploymentType())->all('name')),
            'jobLevels' => $this->safeLookup(fn (): array => (new JobLevel())->all('sort_order')),
            'salaryRanges' => $this->safeLookup(fn (): array => (new SalaryRange())->all('sort_order')),
            'workArrangements' => $this->safeLookup(fn (): array => (new WorkArrangement())->all('name')),
            'queryString' => $this->queryStringWithoutPage(),
        ];

        if ($this->isAjaxSearchRequest()) {
            View::render('jobs/partials/results', $data, null);
            return;
        }

        $this->view('jobs/index', $data);
    }

    public function show(): void
    {
        if (! in_array(($_SESSION['user']['role'] ?? null), ['job_seeker', 'employer', 'admin'], true)) {
            $this->redirect('/login');
        }

        $jobId = (int) ($_GET['id'] ?? 0);
        $job = $jobId > 0 ? (new JobVacancy())->findDetailed($jobId) : null;

        if ($job === null) {
            http_response_code(404);
            $this->view('errors/404', ['path' => '/jobs/show']);
            return;
        }

        $isEmployerOwner = ($_SESSION['user']['role'] ?? null) === 'employer'
            && (int) $job['employer_user_id'] === (int) $_SESSION['user']['id'];
        $isAdmin = ($_SESSION['user']['role'] ?? null) === 'admin';

        if (($job['status'] ?? '') !== 'active' && ! $isEmployerOwner && ! $isAdmin) {
            http_response_code(404);
            $this->view('errors/404', ['path' => '/jobs/show']);
            return;
        }

        $this->view('jobs/show', [
            'title' => $job['job_title'] ?? 'Job Detail',
            'metaDescription' => $this->jobMetaDescription($job),
            'canonicalPath' => '/jobs/show',
            'structuredData' => $this->jobStructuredData($job),
            'job' => $job,
            'requiredSkills' => (new JobVacancySkill())->findByJobVacancyId($jobId),
            'isEmployerOwner' => $isEmployerOwner,
        ]);
    }

    private function filtersFromQuery(): array
    {
        return [
            'keyword' => trim((string) ($_GET['keyword'] ?? '')),
            'job_category_id' => (int) ($_GET['job_category_id'] ?? 0),
            'country_id' => (int) ($_GET['country_id'] ?? 0),
            'city_id' => (int) ($_GET['city_id'] ?? 0),
            'skill_ids' => $this->intList($_GET['skill_ids'] ?? []),
            'employment_type_id' => (int) ($_GET['employment_type_id'] ?? 0),
            'job_level_id' => (int) ($_GET['job_level_id'] ?? 0),
            'salary_range_id' => (int) ($_GET['salary_range_id'] ?? 0),
            'work_arrangement_id' => (int) ($_GET['work_arrangement_id'] ?? 0),
            'sort' => $this->sortValue((string) ($_GET['sort'] ?? 'recent')),
        ];
    }

    private function intList(mixed $value): array
    {
        if (! is_array($value)) {
            $value = [$value];
        }

        return array_values(array_unique(array_filter(
            array_map(static fn (mixed $item): int => (int) $item, $value),
            static fn (int $item): bool => $item > 0
        )));
    }

    private function sortValue(string $sort): string
    {
        return in_array($sort, ['recent', 'salary_asc', 'salary_desc', 'title'], true) ? $sort : 'recent';
    }

    private function queryStringWithoutPage(): string
    {
        $query = $_GET;
        unset($query['page'], $query['ajax']);

        return http_build_query($query);
    }

    private function isAjaxSearchRequest(): bool
    {
        return ($_GET['ajax'] ?? '') === '1'
            || str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'text/html+partial')
            || strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'fetch';
    }

    private function safeLookup(callable $loader): array
    {
        try {
            return $loader();
        } catch (Throwable) {
            return [];
        }
    }

    private function safeCount(callable $loader): int
    {
        try {
            return $loader();
        } catch (Throwable) {
            return 0;
        }
    }

    private function jobMetaDescription(array $job): string
    {
        $parts = array_values(array_filter([
            $job['job_title'] ?? null,
            $job['company_name'] ?? null,
            $job['city_name'] ?? null,
            $job['salary_range'] ?? null,
        ]));

        return implode(' at ', array_slice($parts, 0, 2))
            . (isset($parts[2]) ? ' in ' . $parts[2] : '')
            . '. View salary, required skills, responsibilities, and employer information on OneCV.';
    }

    private function jobStructuredData(array $job): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $job['job_title'] ?? 'Job Vacancy',
            'description' => trim(strip_tags((string) ($job['responsibilities'] ?? 'Job vacancy on OneCV.'))),
            'datePosted' => isset($job['created_at']) ? date('Y-m-d', strtotime((string) $job['created_at'])) : date('Y-m-d'),
            'employmentType' => $job['employment_type'] ?? null,
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => $job['company_name'] ?? 'OneCV Employer',
                'logo' => $job['company_avatar_url'] ?? null,
            ],
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $job['city_name'] ?? null,
                    'addressRegion' => $job['district_name'] ?? null,
                    'addressCountry' => $job['country_name'] ?? null,
                ],
            ],
        ];
    }
}
