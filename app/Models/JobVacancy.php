<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class JobVacancy extends Model
{
    protected string $table = 'job_vacancies';

    public function forEmployer(int $employerUserId): array
    {
        return $this->get(
            'SELECT job_vacancies.*, job_titles.name AS job_title, job_categories.name AS job_category
             FROM `job_vacancies`
             INNER JOIN `job_titles` ON job_titles.id = job_vacancies.job_title_id
             INNER JOIN `job_categories` ON job_categories.id = job_vacancies.job_category_id
             WHERE job_vacancies.employer_user_id = :employer_user_id
             ORDER BY job_vacancies.updated_at DESC',
            ['employer_user_id' => $employerUserId]
        );
    }
}
