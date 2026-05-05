<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
// models for job vacancy creation
use App\Models\Company;
use App\Models\JobVacancy;
use App\Models\JobVacancySkill;
// model A
use App\Models\JobTitle;
use App\Models\JobCategory;
use App\Models\EmploymentType;
use App\Models\Industry;
use App\Models\JobLevel;
// model B 
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\WorkArrangement;
// model C
use App\Models\SalaryRange;
use App\Models\SalaryType;
// model E
use App\Models\Skill;
use App\Models\SkillProficiencyLevel;
// model F
use App\Models\DegreeLevel;
use App\Services\GoogleCloudStorage;
use RuntimeException;
use Throwable;

class JobVacancyController extends Controller
{
    public function index(): void
    {
        $this->requireEmployer();

        $this->view('employer/jobs/index', [
            'title' => 'My Jobs',
            'jobs' => $this->safeCall(fn (): array => (new JobVacancy())->forEmployer((int) $_SESSION['user']['id'])),
        ]);
    }

    public function create(): void
    {
        $this->requireEmployer();
        $this->redirect('/employer/jobs/create/basics');
    }

    public function basics(): void
    {
        $this->requireEmployer();

        $this->view('employer/jobs/create-basics', $this->viewData('Post Job - Basics'));
    }

    public function saveBasics(): void
    {
        $this->requireEmployer();

        $this->mergeDraft($this->only([
            'job_title_id',
            'job_category_id',
            'employment_type_id',
            'industry_id',
            'job_level_id',
            'number_of_openings',
        ]));

        $this->flash('success', 'Job basics have been saved as a draft.');
        if (($_POST['next_step'] ?? '') !== 'location') {
            $this->redirect('/employer/jobs/create/basics');
        }

        $this->redirect('/employer/jobs/create/location');
    }

    public function location(): void
    {
        $this->requireEmployer();

        $this->view('employer/jobs/create-location', $this->viewData('Post Job - Location'));
    }

    public function saveLocation(): void
    {
        $this->requireEmployer();

        $this->mergeDraft($this->only([
            'company_name',
            'company_description',
            'country_id',
            'city_id',
            'district_id',
            'work_arrangement_id',
            'salary_range_id',
            'salary_type_id',
            'benefits',
        ]));

        if ($this->hasCompanyAvatarUpload()) {
            try {
                $_SESSION['job_vacancy_draft']['company_avatar_url'] = $this->uploadCompanyAvatar();
            } catch (Throwable $exception) {
                $this->flash('errors', ['Company avatar upload failed: ' . $exception->getMessage()]);
                $this->redirect('/employer/jobs/create/location');
            }
        }

        $this->flash('success', 'Location and compensation have been saved as a draft.');
        if (($_POST['next_step'] ?? '') !== 'requirements') {
            $this->redirect('/employer/jobs/create/location');
        }

        $this->redirect('/employer/jobs/create/requirements');
    }

    public function requirements(): void
    {
        $this->requireEmployer();

        $this->view('employer/jobs/create-requirements', $this->viewData('Post Job - Requirements'));
    }

    public function saveRequirements(): void
    {
        $this->requireEmployer();

        $this->mergeDraft($this->only([
            'responsibilities',
            'required_qualifications',
            'preferred_skills',
            'additional_notes',
            'minimum_degree_level_id',
            'minimum_years_experience',
        ]));

        $_SESSION['job_vacancy_draft']['skills'] = $_POST['skills'] ?? [];

        $this->flash('success', 'Requirements have been saved as a draft.');
        if (($_POST['next_step'] ?? '') !== 'review') {
            $this->redirect('/employer/jobs/create/requirements');
        }

        $this->redirect('/employer/jobs/create/review');
    }

    public function review(): void
    {
        $this->requireEmployer();

        $this->view('employer/jobs/create-review', $this->viewData('Post Job - Review'));
    }

    public function publish(): void
    {
        $this->requireEmployer();

        $draft = $_SESSION['job_vacancy_draft'] ?? [];

        try {
            $jobVacancyId = $this->publishDraft((int) $_SESSION['user']['id'], $draft);
        } catch (Throwable $exception) {
            $this->flash('errors', ['Could not publish this job vacancy: ' . $exception->getMessage()]);
            $this->redirect('/employer/jobs/create/review');
        }

        unset($_SESSION['job_vacancy_draft']);

        $this->flash('success', 'Job vacancy has been published successfully.');
        $this->redirect('/employer/jobs');
    }

    private function viewData(string $title): array
    {
        return [
            'title' => $title,
            'draft' => $_SESSION['job_vacancy_draft'] ?? [],
            'companies' => $this->safeCall(fn (): array => (new Company())->forEmployer((int) $_SESSION['user']['id'])),
            'jobTitles' => $this->safeCall(fn (): array => (new JobTitle())->all('name')),
            'jobCategories' => $this->safeCall(fn (): array => (new JobCategory())->all('name')),
            'employmentTypes' => $this->safeCall(fn (): array => (new EmploymentType())->all('name')),
            'industries' => $this->safeCall(fn (): array => (new Industry())->all('name')),
            'jobLevels' => $this->safeCall(fn (): array => (new JobLevel())->all('sort_order')),
            'countries' => $this->safeCall(fn (): array => (new Country())->all('name')),
            'cities' => $this->safeCall(fn (): array => (new City())->all('name')),
            'districts' => $this->safeCall(fn (): array => (new District())->all('name')),
            'workArrangements' => $this->safeCall(fn (): array => (new WorkArrangement())->all('name')),
            'salaryRanges' => $this->safeCall(fn (): array => (new SalaryRange())->all('sort_order')),
            'salaryTypes' => $this->safeCall(fn (): array => (new SalaryType())->all('name')),
            'degreeLevels' => $this->safeCall(fn (): array => (new DegreeLevel())->all('sort_order')),
            'skills' => $this->safeCall(fn (): array => (new Skill())->all('name')),
            'proficiencyLevels' => $this->safeCall(fn (): array => (new SkillProficiencyLevel())->all('level_value')),
        ];
    }

    private function safeCall(callable $callback): array
    {
        try {
            return $callback();
        } catch (Throwable) {
            return [];
        }
    }

    private function mergeDraft(array $data): void
    {
        $_SESSION['job_vacancy_draft'] = array_merge($_SESSION['job_vacancy_draft'] ?? [], $data);
    }

    private function publishDraft(int $employerUserId, array $draft): int
    {
        $this->validatePublishDraft($draft);
        $skills = $this->sanitizeRequiredSkills($draft['skills'] ?? []);

        return (int) Database::transaction(function () use ($employerUserId, $draft, $skills): int {
            $companyId = (new Company())->createOrUpdateForEmployer($employerUserId, [
                'name' => trim((string) $draft['company_name']),
                'avatar_url' => trim((string) ($draft['company_avatar_url'] ?? '')) ?: null,
                'description' => trim((string) $draft['company_description']),
            ]);

            $jobVacancyId = (new JobVacancy())->create([
                'employer_user_id' => $employerUserId,
                'company_id' => $companyId,
                'job_title_id' => (int) $draft['job_title_id'],
                'job_category_id' => (int) $draft['job_category_id'],
                'employment_type_id' => (int) $draft['employment_type_id'],
                'industry_id' => (int) $draft['industry_id'],
                'job_level_id' => (int) $draft['job_level_id'],
                'number_of_openings' => (int) $draft['number_of_openings'],
                'country_id' => (int) $draft['country_id'],
                'city_id' => (int) $draft['city_id'],
                'district_id' => ($draft['district_id'] ?? '') === '' ? null : (int) $draft['district_id'],
                'work_arrangement_id' => (int) $draft['work_arrangement_id'],
                'salary_range_id' => (int) $draft['salary_range_id'],
                'salary_type_id' => (int) $draft['salary_type_id'],
                'benefits' => trim((string) ($draft['benefits'] ?? '')) ?: null,
                'responsibilities' => trim((string) $draft['responsibilities']),
                'required_qualifications' => trim((string) $draft['required_qualifications']),
                'preferred_skills' => trim((string) ($draft['preferred_skills'] ?? '')) ?: null,
                'additional_notes' => trim((string) ($draft['additional_notes'] ?? '')) ?: null,
                'minimum_degree_level_id' => (int) $draft['minimum_degree_level_id'],
                'minimum_years_experience' => (int) $draft['minimum_years_experience'],
                'status' => 'active',
            ]);

            (new JobVacancySkill())->createManyForVacancy($jobVacancyId, $skills);

            return $jobVacancyId;
        });
    }

    private function validatePublishDraft(array $draft): void
    {
        $requiredFields = [
            'company_name' => 'Company name is required.',
            'company_description' => 'Company description is required.',
            'job_title_id' => 'Job title is required.',
            'job_category_id' => 'Job category is required.',
            'employment_type_id' => 'Employment type is required.',
            'industry_id' => 'Industry is required.',
            'job_level_id' => 'Job level is required.',
            'number_of_openings' => 'Number of openings is required.',
            'country_id' => 'Country is required.',
            'city_id' => 'City is required.',
            'work_arrangement_id' => 'Work arrangement is required.',
            'salary_range_id' => 'Salary range is required.',
            'salary_type_id' => 'Salary type is required.',
            'responsibilities' => 'Responsibilities are required.',
            'required_qualifications' => 'Required qualifications are required.',
            'minimum_degree_level_id' => 'Minimum degree level is required.',
            'minimum_years_experience' => 'Minimum years experience is required.',
        ];

        foreach ($requiredFields as $field => $message) {
            if (trim((string) ($draft[$field] ?? '')) === '') {
                throw new RuntimeException($message);
            }
        }

        if ((int) $draft['number_of_openings'] < 1) {
            throw new RuntimeException('Number of openings must be at least 1.');
        }

        if ((int) $draft['minimum_years_experience'] < 0) {
            throw new RuntimeException('Minimum years experience cannot be negative.');
        }
    }

    private function sanitizeRequiredSkills(array $rows): array
    {
        $skills = [];

        foreach ($rows as $row) {
            if (! is_array($row) || trim((string) ($row['skill_id'] ?? '')) === '') {
                continue;
            }

            if (trim((string) ($row['minimum_proficiency_level_id'] ?? '')) === '') {
                throw new RuntimeException('Each required skill must include a minimum proficiency level.');
            }

            $skills[] = [
                'skill_id' => (int) $row['skill_id'],
                'minimum_proficiency_level_id' => (int) $row['minimum_proficiency_level_id'],
            ];
        }

        if ($skills === []) {
            throw new RuntimeException('Please add at least one required skill.');
        }

        (new JobVacancySkill())->validateMaxSkills($skills);

        return $skills;
    }

    private function hasCompanyAvatarUpload(): bool
    {
        return isset($_FILES['company_avatar']) && ($_FILES['company_avatar']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }

    private function uploadCompanyAvatar(): string
    {
        $file = $_FILES['company_avatar'] ?? null;

        if (! is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('The selected image could not be uploaded.');
        }

        $mimeType = (string) ($file['type'] ?? '');
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        if (! in_array($mimeType, $allowedTypes, true)) {
            throw new \RuntimeException('Please choose a JPG, PNG, WEBP, or GIF image.');
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');
        $content = is_file($tmpName) ? file_get_contents($tmpName) : false;

        if ($content === false) {
            throw new \RuntimeException('The uploaded image could not be read.');
        }

        $extension = match ($mimeType) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'jpg',
        };
        $objectName = sprintf('company-avatars/%d/%s.%s', (int) $_SESSION['user']['id'], bin2hex(random_bytes(12)), $extension);

        return (new GoogleCloudStorage())->uploadPublicObject($objectName, $content, $mimeType);
    }

    private function requireEmployer(): void
    {
        if (($_SESSION['user']['role'] ?? null) !== 'employer') {
            $this->redirect('/login');
        }
    }
}
