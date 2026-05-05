<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use InvalidArgumentException;

class JobVacancySkill extends Model
{
    protected string $table = 'job_vacancy_skills';

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
}
