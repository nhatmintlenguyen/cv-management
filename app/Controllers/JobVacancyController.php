<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
// models for job vacancy creation
use App\Models\JobVacancy;
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
            'country_id',
            'city_id',
            'district_id',
            'work_arrangement_id',
            'salary_range_id',
            'salary_type_id',
            'benefits',
        ]));

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

        $this->flash('success', 'Publish action placeholder is ready. The next implementation step will persist this draft to job_vacancies.');
        $this->redirect('/employer/jobs');
    }

    private function viewData(string $title): array
    {
        return [
            'title' => $title,
            'draft' => $_SESSION['job_vacancy_draft'] ?? [],
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

    private function requireEmployer(): void
    {
        if (($_SESSION['user']['role'] ?? null) !== 'employer') {
            $this->redirect('/login');
        }
    }
}
