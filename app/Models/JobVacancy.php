<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class JobVacancy extends Model
{
    protected string $table = 'job_vacancies';

    private function detailSelectSql(): string
    {
        return 'SELECT job_vacancies.*, job_titles.name AS job_title, job_categories.name AS job_category,
                    employment_types.name AS employment_type,
                    industries.name AS industry_name,
                    job_levels.name AS job_level,
                    countries.name AS country_name,
                    cities.name AS city_name,
                    districts.name AS district_name,
                    work_arrangements.name AS work_arrangement,
                    salary_ranges.label AS salary_range,
                    salary_types.name AS salary_type,
                    degree_levels.name AS minimum_degree_level,
                    companies.name AS company_name,
                    companies.avatar_url AS company_avatar_url,
                    companies.description AS company_description,
                    (
                        SELECT COUNT(*)
                        FROM `job_vacancy_skills`
                        WHERE job_vacancy_skills.job_vacancy_id = job_vacancies.id
                    ) AS required_skill_count
             FROM `job_vacancies`
             INNER JOIN `job_titles` ON job_titles.id = job_vacancies.job_title_id
             INNER JOIN `job_categories` ON job_categories.id = job_vacancies.job_category_id
             INNER JOIN `companies` ON companies.id = job_vacancies.company_id
             INNER JOIN `employment_types` ON employment_types.id = job_vacancies.employment_type_id
             INNER JOIN `industries` ON industries.id = job_vacancies.industry_id
             INNER JOIN `job_levels` ON job_levels.id = job_vacancies.job_level_id
             INNER JOIN `countries` ON countries.id = job_vacancies.country_id
             INNER JOIN `cities` ON cities.id = job_vacancies.city_id
             LEFT JOIN `districts` ON districts.id = job_vacancies.district_id
             INNER JOIN `work_arrangements` ON work_arrangements.id = job_vacancies.work_arrangement_id
             INNER JOIN `salary_ranges` ON salary_ranges.id = job_vacancies.salary_range_id
             INNER JOIN `salary_types` ON salary_types.id = job_vacancies.salary_type_id
             INNER JOIN `degree_levels` ON degree_levels.id = job_vacancies.minimum_degree_level_id';
    }

    public function forEmployer(int $employerUserId): array
    {
        return $this->get(
            $this->detailSelectSql() . '
             WHERE job_vacancies.employer_user_id = :employer_user_id
             ORDER BY job_vacancies.updated_at DESC',
            ['employer_user_id' => $employerUserId]
        );
    }

    public function findDetailed(int $id): ?array
    {
        return $this->first(
            $this->detailSelectSql() . '
             WHERE job_vacancies.id = :id
             LIMIT 1',
            ['id' => $id]
        );
    }

    public function activeJobs(): array
    {
        return $this->get(
            $this->detailSelectSql() . "
             WHERE job_vacancies.status = 'active'
             ORDER BY job_vacancies.updated_at DESC"
        );
    }

    public function belongsToEmployer(int $id, int $employerUserId): bool
    {
        return $this->first(
            'SELECT 1
             FROM `job_vacancies`
             WHERE `id` = :id AND `employer_user_id` = :employer_user_id
             LIMIT 1',
            ['id' => $id, 'employer_user_id' => $employerUserId]
        ) !== null;
    }

    public function toggleStatusForEmployer(int $id, int $employerUserId): bool
    {
        return $this->query(
            "UPDATE `job_vacancies`
             SET `status` = CASE WHEN `status` = 'active' THEN 'inactive' ELSE 'active' END
             WHERE `id` = :id AND `employer_user_id` = :employer_user_id",
            ['id' => $id, 'employer_user_id' => $employerUserId]
        )->rowCount() > 0;
    }

    public function deleteForEmployer(int $id, int $employerUserId): bool
    {
        return $this->query(
            'DELETE FROM `job_vacancies`
             WHERE `id` = :id AND `employer_user_id` = :employer_user_id',
            ['id' => $id, 'employer_user_id' => $employerUserId]
        )->rowCount() > 0;
    }
}
