<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class CVEducation extends Model
{
    protected string $table = 'cv_educations';

    public function findByCvId(int $cvId): array
    {
        return $this->get(
            'SELECT cv_educations.*,
                    institutions.name AS institution_name,
                    degree_levels.name AS degree_level_name,
                    majors.name AS major_name
             FROM `cv_educations`
             INNER JOIN `institutions` ON institutions.id = cv_educations.institution_id
             INNER JOIN `degree_levels` ON degree_levels.id = cv_educations.degree_level_id
             INNER JOIN `majors` ON majors.id = cv_educations.major_id
             WHERE cv_educations.cv_id = :cv_id
             ORDER BY cv_educations.display_order ASC, cv_educations.start_year DESC',
            ['cv_id' => $cvId]
        );
    }

    public function deleteByCvId(int $cvId): int
    {
        return $this->query(
            'DELETE FROM `cv_educations` WHERE `cv_id` = :cv_id',
            ['cv_id' => $cvId]
        )->rowCount();
    }

    public function replaceForCv(int $cvId, array $items): void
    {
        Database::transaction(function () use ($cvId, $items): void {
            $this->deleteByCvId($cvId);

            foreach (array_values($items) as $index => $item) {
                $item['cv_id'] = $cvId;
                $item['display_order'] = $item['display_order'] ?? $index;
                $this->create($item);
            }
        });
    }
}
