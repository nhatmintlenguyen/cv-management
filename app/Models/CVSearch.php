<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class CVSearch extends Model
{
    protected string $table = 'cvs';

    public function search(array $filters = []): array
    {
        $params = [];
        $where = [];
        $joins = [];

        $select = 'SELECT DISTINCT cvs.*,
                          cv_categories.name AS category_name,
                          countries.name AS country_name,
                          cities.name AS city_name,
                          COALESCE(experience.total_years, 0) AS approximate_experience_years';

        $from = ' FROM `cvs`
                  INNER JOIN `cv_categories` ON cv_categories.id = cvs.cv_category_id
                  INNER JOIN `countries` ON countries.id = cvs.country_id
                  INNER JOIN `cities` ON cities.id = cvs.city_id
                  LEFT JOIN (
                    SELECT cv_id,
                           SUM(
                             CASE
                               WHEN is_current = TRUE THEN YEAR(CURDATE()) - start_year
                               ELSE end_year - start_year
                             END
                           ) AS total_years
                    FROM `cv_work_histories`
                    GROUP BY cv_id
                  ) AS experience ON experience.cv_id = cvs.id';

        if (! empty($filters['keyword'])) {
            $where[] = '(cvs.full_name LIKE :keyword
                         OR cvs.summary LIKE :keyword
                         OR EXISTS (
                           SELECT 1 FROM `cv_work_histories`
                           WHERE cv_work_histories.cv_id = cvs.id
                             AND (
                               cv_work_histories.company_name LIKE :keyword
                               OR cv_work_histories.job_description LIKE :keyword
                             )
                         )
                         OR EXISTS (
                           SELECT 1 FROM `cv_educations`
                           WHERE cv_educations.cv_id = cvs.id
                             AND cv_educations.description LIKE :keyword
                         )
                         OR EXISTS (
                           SELECT 1 FROM `cv_certificates`
                           WHERE cv_certificates.cv_id = cvs.id
                             AND cv_certificates.description LIKE :keyword
                         ))';
            $params['keyword'] = '%' . $filters['keyword'] . '%';
        }

        if (! empty($filters['category_id'])) {
            $where[] = 'cvs.cv_category_id = :category_id';
            $params['category_id'] = (int) $filters['category_id'];
        }

        if (! empty($filters['country_id'])) {
            $where[] = 'cvs.country_id = :country_id';
            $params['country_id'] = (int) $filters['country_id'];
        }

        if (! empty($filters['city_id'])) {
            $where[] = 'cvs.city_id = :city_id';
            $params['city_id'] = (int) $filters['city_id'];
        }

        if (! empty($filters['degree_level_id'])) {
            $where[] = 'EXISTS (
                SELECT 1 FROM `cv_educations`
                WHERE cv_educations.cv_id = cvs.id
                  AND cv_educations.degree_level_id = :degree_level_id
            )';
            $params['degree_level_id'] = (int) $filters['degree_level_id'];
        }

        if (! empty($filters['skill_ids']) && is_array($filters['skill_ids'])) {
            $skillPlaceholders = [];

            foreach (array_values($filters['skill_ids']) as $index => $skillId) {
                $placeholder = 'skill_id_' . $index;
                $skillPlaceholders[] = ':' . $placeholder;
                $params[$placeholder] = (int) $skillId;
            }

            if ($skillPlaceholders !== []) {
                $where[] = 'EXISTS (
                    SELECT 1 FROM `cv_skills`
                    WHERE cv_skills.cv_id = cvs.id
                      AND cv_skills.skill_id IN (' . implode(', ', $skillPlaceholders) . ')
                )';
            }
        }

        if (! empty($filters['min_proficiency'])) {
            $where[] = 'EXISTS (
                SELECT 1
                FROM `cv_skills`
                INNER JOIN `skill_proficiency_levels`
                    ON skill_proficiency_levels.id = cv_skills.proficiency_level_id
                WHERE cv_skills.cv_id = cvs.id
                  AND skill_proficiency_levels.level_value >= :min_proficiency
            )';
            $params['min_proficiency'] = (int) $filters['min_proficiency'];
        }

        $sql = $select . $from . implode(' ', $joins);

        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ' . $this->sortSql((string) ($filters['sort'] ?? 'recent'));

        return $this->get($sql, $params);
    }

    private function sortSql(string $sort): string
    {
        return match ($sort) {
            'alphabetical' => 'ORDER BY cvs.full_name ASC',
            'experience' => 'ORDER BY approximate_experience_years DESC, cvs.updated_at DESC',
            default => 'ORDER BY cvs.updated_at DESC',
        };
    }
}
