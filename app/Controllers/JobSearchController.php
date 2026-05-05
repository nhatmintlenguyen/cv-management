<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\JobVacancy;
use App\Models\JobVacancySkill;

class JobSearchController extends Controller
{
    public function index(): void
    {
        if (! in_array(($_SESSION['user']['role'] ?? null), ['job_seeker', 'employer'], true)) {
            $this->redirect('/login');
        }

        $this->view('jobs/index', [
            'title' => 'Job Search',
            'jobs' => (new JobVacancy())->activeJobs(),
        ]);
    }

    public function show(): void
    {
        if (! in_array(($_SESSION['user']['role'] ?? null), ['job_seeker', 'employer'], true)) {
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

        if (($job['status'] ?? '') !== 'active' && ! $isEmployerOwner) {
            http_response_code(404);
            $this->view('errors/404', ['path' => '/jobs/show']);
            return;
        }

        $this->view('jobs/show', [
            'title' => $job['job_title'] ?? 'Job Detail',
            'job' => $job,
            'requiredSkills' => (new JobVacancySkill())->findByJobVacancyId($jobId),
            'isEmployerOwner' => $isEmployerOwner,
        ]);
    }
}
