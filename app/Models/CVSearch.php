<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class CVSearch extends Model
{
    protected string $table = 'cvs';

    public function search(array $filters = []): array
    {
        $query = $this->buildQuery($filters);
        $limit = max(1, min(48, (int) ($filters['limit'] ?? 6)));
        $offset = max(0, (int) ($filters['offset'] ?? 0));

        $sql = $query['select'] . $query['from'] . $query['where'] . $query['order'] . ' LIMIT ' . $limit . ' OFFSET ' . $offset;
        $params = $query['params'];

        return $this->get($sql, $params);
    }

    public function count(array $filters = []): int
    {
        $query = $this->buildQuery($filters);
        $row = $this->first(
            'SELECT COUNT(*) AS total FROM (' . $query['select'] . $query['from'] . $query['where'] . ') AS filtered_cvs',
            $query['params']
        );

        return (int) ($row['total'] ?? 0);
    }

    private function buildQuery(array $filters = []): array
    {
        $params = [];
        $where = ['cvs.is_completed = 1'];

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
            $keywordWhere = '(cvs.full_name LIKE :keyword
                         OR cvs.summary LIKE :keyword
                         OR cvs.email LIKE :keyword
                         OR cvs.phone_number LIKE :keyword
                         OR cvs.street_address LIKE :keyword
                         OR cvs.postal_code LIKE :keyword
                         OR cv_categories.name LIKE :keyword
                         OR countries.name LIKE :keyword
                         OR cities.name LIKE :keyword
                         OR EXISTS (
                           SELECT 1
                           FROM `districts`
                           WHERE districts.id = cvs.district_id
                             AND districts.name LIKE :keyword
                         )
                         OR EXISTS (
                           SELECT 1
                           FROM `genders`
                           WHERE genders.id = cvs.gender_id
                             AND genders.name LIKE :keyword
                         )
                         OR EXISTS (
                           SELECT 1
                           FROM `cv_work_histories`
                           INNER JOIN `job_titles`
                             ON job_titles.id = cv_work_histories.job_title_id
                           INNER JOIN `employment_types`
                             ON employment_types.id = cv_work_histories.employment_type_id
                           INNER JOIN `industries`
                             ON industries.id = cv_work_histories.industry_id
                           WHERE cv_work_histories.cv_id = cvs.id
                             AND (
                               cv_work_histories.company_name LIKE :keyword
                               OR cv_work_histories.job_description LIKE :keyword
                               OR job_titles.name LIKE :keyword
                               OR employment_types.name LIKE :keyword
                               OR industries.name LIKE :keyword
                             )
                         )
                         OR EXISTS (
                           SELECT 1
                           FROM `cv_educations`
                           INNER JOIN `institutions`
                             ON institutions.id = cv_educations.institution_id
                           INNER JOIN `degree_levels`
                             ON degree_levels.id = cv_educations.degree_level_id
                           INNER JOIN `majors`
                             ON majors.id = cv_educations.major_id
                           WHERE cv_educations.cv_id = cvs.id
                             AND (
                               cv_educations.description LIKE :keyword
                               OR institutions.name LIKE :keyword
                               OR degree_levels.name LIKE :keyword
                               OR majors.name LIKE :keyword
                             )
                         )
                         OR EXISTS (
                           SELECT 1
                           FROM `cv_certificates`
                           INNER JOIN `certificate_names`
                             ON certificate_names.id = cv_certificates.certificate_name_id
                           INNER JOIN `issuing_organizations`
                             ON issuing_organizations.id = cv_certificates.issuing_organization_id
                           WHERE cv_certificates.cv_id = cvs.id
                             AND (
                               cv_certificates.description LIKE :keyword
                               OR certificate_names.name LIKE :keyword
                               OR issuing_organizations.name LIKE :keyword
                             )
                         )
                         OR EXISTS (
                           SELECT 1
                           FROM `cv_skills`
                           INNER JOIN `skills`
                             ON skills.id = cv_skills.skill_id
                           INNER JOIN `skill_proficiency_levels`
                             ON skill_proficiency_levels.id = cv_skills.proficiency_level_id
                           WHERE cv_skills.cv_id = cvs.id
                             AND (
                               skills.name LIKE :keyword
                               OR skill_proficiency_levels.name LIKE :keyword
                               OR skill_proficiency_levels.level_value = :keyword_level
                             )
                         ))';

            $keywordValue = '%' . $filters['keyword'] . '%';
            $keywordLevel = ctype_digit((string) $filters['keyword']) ? (int) $filters['keyword'] : -1;
            $keywordIndex = 0;
            $keywordWhere = preg_replace_callback(
                '/:(keyword|keyword_level)\b/',
                static function (array $matches) use (&$params, &$keywordIndex, $keywordValue, $keywordLevel): string {
                    $placeholder = $matches[1] . '_' . $keywordIndex++;
                    $params[$placeholder] = $matches[1] === 'keyword' ? $keywordValue : $keywordLevel;

                    return ':' . $placeholder;
                },
                $keywordWhere
            );
            $where[] = $keywordWhere;
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
            foreach (array_values($filters['skill_ids']) as $index => $skillId) {
                $skillId = (int) $skillId;

                if ($skillId <= 0) {
                    continue;
                }

                $skillPlaceholder = 'skill_id_' . $index;
                $params[$skillPlaceholder] = $skillId;

                if (! empty($filters['min_proficiency'])) {
                    $proficiencyPlaceholder = 'skill_min_proficiency_' . $index;
                    $params[$proficiencyPlaceholder] = (int) $filters['min_proficiency'];
                    $where[] = 'EXISTS (
                        SELECT 1
                        FROM `cv_skills`
                        INNER JOIN `skill_proficiency_levels`
                            ON skill_proficiency_levels.id = cv_skills.proficiency_level_id
                        WHERE cv_skills.cv_id = cvs.id
                          AND cv_skills.skill_id = :' . $skillPlaceholder . '
                          AND skill_proficiency_levels.level_value >= :' . $proficiencyPlaceholder . '
                    )';
                    continue;
                }

                $where[] = 'EXISTS (
                    SELECT 1 FROM `cv_skills`
                    WHERE cv_skills.cv_id = cvs.id
                      AND cv_skills.skill_id = :' . $skillPlaceholder . '
                )';
            }
        }

        if (empty($filters['skill_ids']) && ! empty($filters['min_proficiency'])) {
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

        $whereSql = '';
        if ($where !== []) {
            $whereSql = ' WHERE ' . implode(' AND ', $where);
        }

        return [
            'select' => $select,
            'from' => $from,
            'where' => $whereSql,
            'order' => ' ' . $this->sortSql((string) ($filters['sort'] ?? 'recent')),
            'params' => $params,
        ];
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
