<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use InvalidArgumentException;

class JobVacancySkill extends Model
{
    protected string $table = 'job_vacancy_skills';

    public function findByJobVacancyId(int $jobVacancyId): array
    {
        return $this->get(
            'SELECT job_vacancy_skills.*,
                    skills.name AS skill_name,
                    skill_proficiency_levels.name AS proficiency_name,
                    skill_proficiency_levels.level_value
             FROM `job_vacancy_skills`
             INNER JOIN `skills` ON skills.id = job_vacancy_skills.skill_id
             INNER JOIN `skill_proficiency_levels`
                ON skill_proficiency_levels.id = job_vacancy_skills.minimum_proficiency_level_id
             WHERE job_vacancy_skills.job_vacancy_id = :job_vacancy_id
             ORDER BY skill_proficiency_levels.level_value DESC, skills.name ASC',
            ['job_vacancy_id' => $jobVacancyId]
        );
    }

    public function deleteByJobVacancyId(int $jobVacancyId): int
    {
        return $this->query(
            'DELETE FROM `job_vacancy_skills` WHERE `job_vacancy_id` = :job_vacancy_id',
            ['job_vacancy_id' => $jobVacancyId]
        )->rowCount();
    }

    public function validateMaxSkills(array $items): void
    {
        if (count($items) > 5) {
            throw new InvalidArgumentException('A job vacancy can have at most 5 required skills.');
        }

        $skillIds = array_map(fn (array $item): int => (int) ($item['skill_id'] ?? 0), $items);
        $skillIds = array_filter($skillIds);

        if (count($skillIds) !== count(array_unique($skillIds))) {
            throw new InvalidArgumentException('A job vacancy cannot contain duplicate required skills.');
        }
    }

    public function createManyForVacancy(int $jobVacancyId, array $items): void
    {
        $this->validateMaxSkills($items);

        foreach ($items as $item) {
            $this->create([
                'job_vacancy_id' => $jobVacancyId,
                'skill_id' => (int) $item['skill_id'],
                'minimum_proficiency_level_id' => (int) $item['minimum_proficiency_level_id'],
            ]);
        }
    }

    public function replaceForVacancy(int $jobVacancyId, array $items): void
    {
        $this->validateMaxSkills($items);
        $this->deleteByJobVacancyId($jobVacancyId);
        $this->createManyForVacancy($jobVacancyId, $items);
    }
}
