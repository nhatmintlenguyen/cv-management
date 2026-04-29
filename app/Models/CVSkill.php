<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;
use InvalidArgumentException;

class CVSkill extends Model
{
    protected string $table = 'cv_skills';

    public function findByCvId(int $cvId): array
    {
        return $this->get(
            'SELECT cv_skills.*,
                    skills.name AS skill_name,
                    skill_proficiency_levels.name AS proficiency_name,
                    skill_proficiency_levels.level_value
             FROM `cv_skills`
             INNER JOIN `skills` ON skills.id = cv_skills.skill_id
             INNER JOIN `skill_proficiency_levels`
                ON skill_proficiency_levels.id = cv_skills.proficiency_level_id
             WHERE cv_skills.cv_id = :cv_id
             ORDER BY skill_proficiency_levels.level_value DESC, skills.name ASC',
            ['cv_id' => $cvId]
        );
    }

    public function countByCvId(int $cvId): int
    {
        return $this->countWhere('cv_id', $cvId);
    }

    public function deleteByCvId(int $cvId): int
    {
        return $this->query(
            'DELETE FROM `cv_skills` WHERE `cv_id` = :cv_id',
            ['cv_id' => $cvId]
        )->rowCount();
    }

    public function validateMaxSkills(array $items): void
    {
        if (count($items) > 5) {
            throw new InvalidArgumentException('A CV can have at most 5 strongest skills.');
        }

        $skillIds = array_map(fn (array $item): int => (int) ($item['skill_id'] ?? 0), $items);
        $skillIds = array_filter($skillIds);

        if (count($skillIds) !== count(array_unique($skillIds))) {
            throw new InvalidArgumentException('A CV cannot contain duplicate skills.');
        }
    }

    public function replaceForCv(int $cvId, array $items): void
    {
        $this->validateMaxSkills($items);

        Database::transaction(function () use ($cvId, $items): void {
            $this->deleteByCvId($cvId);

            foreach ($items as $item) {
                $item['cv_id'] = $cvId;
                $this->create($item);
            }
        });
    }
}
