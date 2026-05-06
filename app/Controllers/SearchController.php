<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\View;
use App\Models\City;
use App\Models\Country;
use App\Models\CV;
use App\Models\CVCategory;
use App\Models\CVSearch;
use App\Models\CVTemplate;
use App\Models\DegreeLevel;
use App\Models\Skill;
use App\Models\SkillProficiencyLevel;
use Throwable;

class SearchController extends Controller
{
    private const PER_PAGE = 10;

    public function index(): void
    {
        $this->requireEmployer();

        $filters = $this->filtersFromQuery();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $filters['limit'] = $page * self::PER_PAGE;
        $filters['offset'] = 0;

        $search = new CVSearch();
        $rows = $this->safeLookup(fn (): array => $search->search($filters));
        $total = $this->safeCount(fn (): int => $search->count($filters));

        $data = [
            'title' => 'Find CVs',
            'user' => $_SESSION['user'],
            'filters' => $filters,
            'page' => $page,
            'perPage' => self::PER_PAGE,
            'total' => $total,
            'candidates' => array_map(fn (array $row): array => $this->candidateCard($row), $rows),
            'categories' => $this->safeLookup(fn (): array => (new CVCategory())->all('name')),
            'countries' => $this->safeLookup(fn (): array => (new Country())->all('name')),
            'cities' => $this->safeLookup(fn (): array => (new City())->all('name')),
            'skills' => $this->safeLookup(fn (): array => (new Skill())->all('name')),
            'proficiencyLevels' => $this->safeLookup(fn (): array => (new SkillProficiencyLevel())->ordered()),
            'degreeLevels' => $this->safeLookup(fn (): array => (new DegreeLevel())->ordered()),
            'queryString' => $this->queryStringWithoutPage(),
        ];

        if ($this->isAjaxSearchRequest()) {
            View::render('search/partials/results', $data, null);
            return;
        }

        $this->view('search/index', $data);
    }

    public function show(): void
    {
        $this->requireEmployer();

        $cvId = (int) ($_GET['id'] ?? 0);
        $cv = $cvId > 0 ? (new CV())->findFullCV($cvId) : null;

        if ($cv === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'CV Not Found']);
            return;
        }

        $selected = $this->selectedTemplateForCv($cv);
        $templates = $this->templateOptions();

        $this->view('search/show', [
            'title' => 'Candidate CV',
            'cv' => $cv,
            'selectedTemplateInfo' => $templates[$selected],
            'selectedTemplate' => $selected,
            'mockCv' => $this->presentableCv($cv),
            'backUrl' => $_SERVER['HTTP_REFERER'] ?? '/find-cvs',
        ]);
    }

    private function filtersFromQuery(): array
    {
        return [
            'keyword' => trim((string) ($_GET['keyword'] ?? '')),
            'category_id' => (int) ($_GET['category_id'] ?? 0),
            'country_id' => (int) ($_GET['country_id'] ?? 0),
            'city_id' => (int) ($_GET['city_id'] ?? 0),
            'degree_level_id' => (int) ($_GET['degree_level_id'] ?? 0),
            'skill_ids' => $this->intList($_GET['skill_ids'] ?? []),
            'min_proficiency' => (int) ($_GET['min_proficiency'] ?? 0),
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
        return in_array($sort, ['recent', 'alphabetical', 'experience'], true) ? $sort : 'recent';
    }

    private function candidateCard(array $row): array
    {
        $fullCv = (new CV())->findFullCV((int) $row['id']) ?? $row;
        $firstWork = $fullCv['work_histories'][0] ?? null;
        $skills = array_slice($fullCv['skills'] ?? [], 0, 4);

        return [
            'id' => (int) $row['id'],
            'full_name' => $row['full_name'] ?? '',
            'headline' => $firstWork['job_title_name'] ?? $row['category_name'] ?? 'Candidate',
            'category' => $row['category_name'] ?? '',
            'summary' => $this->excerpt((string) ($row['summary'] ?? '')),
            'avatar' => $fullCv['avatar_url'] ?? null,
            'initials' => $this->initials((string) ($row['full_name'] ?? 'CV')),
            'country' => $row['country_name'] ?? '',
            'city' => $row['city_name'] ?? '',
            'experience_years' => (int) round((float) ($row['approximate_experience_years'] ?? 0)),
            'updated_at' => $row['updated_at'] ?? '',
            'skills' => array_map(static fn (array $skill): string => (string) ($skill['skill_name'] ?? ''), $skills),
        ];
    }

    private function excerpt(string $value, int $length = 120): string
    {
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? '');

        if (strlen($value) <= $length) {
            return $value;
        }

        return rtrim(substr($value, 0, $length - 3)) . '...';
    }

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $letters = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $letters .= strtoupper(substr($part, 0, 1));
        }

        return $letters !== '' ? $letters : 'CV';
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

    private function templateOptions(): array
    {
        return [
            'modern' => [
                'name' => 'Modern Executive',
                'description' => 'Asymmetric, bold typography',
                'accent' => '#a94a4b',
            ],
            'classic' => [
                'name' => 'Classic Editorial',
                'description' => 'Serif, single column focus',
                'accent' => '#574040',
            ],
            'minimal' => [
                'name' => 'Senior Minimal',
                'description' => 'Ultra-clean, high whitespace',
                'accent' => '#000000',
            ],
        ];
    }

    private function selectedTemplateForCv(array $cv): string
    {
        $selected = (new CVTemplate())->keyFromName($cv['template_name'] ?? null);
        $templates = $this->templateOptions();

        return array_key_exists($selected, $templates) ? $selected : 'modern';
    }

    private function presentableCv(array $cv): array
    {
        $firstWork = $cv['work_histories'][0] ?? null;

        return [
            'category' => $cv['category_name'] ?? '',
            'full_name' => $cv['full_name'] ?? '',
            'headline' => $firstWork['job_title_name'] ?? 'Candidate',
            'date_of_birth' => $cv['date_of_birth'] ?? '',
            'gender' => $cv['gender_name'] ?? '',
            'email' => $cv['email'] ?? '',
            'phone_number' => $cv['phone_number'] ?? '',
            'country' => $cv['country_name'] ?? '',
            'city' => $cv['city_name'] ?? '',
            'district' => $cv['district_name'] ?? '',
            'street_address' => $cv['street_address'] ?? '',
            'postal_code' => $cv['postal_code'] ?? '',
            'avatar' => $cv['avatar_url'] ?? 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=400&q=80',
            'summary' => $cv['summary'] ?? '',
            'educations' => array_map(static fn (array $education): array => [
                'institution' => $education['institution_name'] ?? '',
                'degree_level' => $education['degree_level_name'] ?? '',
                'major' => $education['major_name'] ?? '',
                'start_year' => $education['start_year'] ?? '',
                'end_year' => $education['end_year'] ?? '',
                'description' => $education['description'] ?? '',
            ], $cv['educations'] ?? []),
            'work_histories' => array_map(static fn (array $work): array => [
                'job_title' => $work['job_title_name'] ?? '',
                'employment_type' => $work['employment_type_name'] ?? '',
                'industry' => $work['industry_name'] ?? '',
                'company_name' => $work['company_name'] ?? '',
                'start_year' => $work['start_year'] ?? '',
                'end_year' => $work['end_year'] ?? '',
                'is_current' => (bool) ($work['is_current'] ?? false),
                'job_description' => $work['job_description'] ?? '',
            ], $cv['work_histories'] ?? []),
            'certificates' => array_map(static fn (array $certificate): array => [
                'certificate_name' => $certificate['certificate_name'] ?? '',
                'issuing_organization' => $certificate['issuing_organization_name'] ?? '',
                'year_issued' => $certificate['year_issued'] ?? '',
                'description' => $certificate['description'] ?? '',
            ], $cv['certificates'] ?? []),
            'skills' => array_map(static fn (array $skill): array => [
                'skill' => $skill['skill_name'] ?? '',
                'proficiency' => $skill['proficiency_name'] ?? '',
                'level' => (int) ($skill['level_value'] ?? 0),
            ], $cv['skills'] ?? []),
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

    private function safeCount(callable $loader): int
    {
        try {
            return $loader();
        } catch (Throwable) {
            return 0;
        }
    }

    private function requireEmployer(): void
    {
        if (! isset($_SESSION['user'])) {
            $this->redirect('/login?redirect=/find-cvs');
        }

        if (($_SESSION['user']['role'] ?? null) !== 'employer') {
            http_response_code(403);
            $this->view('errors/403', ['title' => 'Forbidden']);
            exit;
        }
    }
}
