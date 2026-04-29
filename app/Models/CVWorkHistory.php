<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class CVWorkHistory extends Model
{
    protected string $table = 'cv_work_histories';

    public function findByCvId(int $cvId): array
    {
        return $this->get(
            'SELECT cv_work_histories.*,
                    job_titles.name AS job_title_name,
                    employment_types.name AS employment_type_name,
                    industries.name AS industry_name
             FROM `cv_work_histories`
             INNER JOIN `job_titles` ON job_titles.id = cv_work_histories.job_title_id
             INNER JOIN `employment_types` ON employment_types.id = cv_work_histories.employment_type_id
             INNER JOIN `industries` ON industries.id = cv_work_histories.industry_id
             WHERE cv_work_histories.cv_id = :cv_id
             ORDER BY cv_work_histories.display_order ASC, cv_work_histories.start_year DESC',
            ['cv_id' => $cvId]
        );
    }

    public function deleteByCvId(int $cvId): int
    {
        return $this->query(
            'DELETE FROM `cv_work_histories` WHERE `cv_id` = :cv_id',
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
                $item['is_current'] = ! empty($item['is_current']) ? 1 : 0;

                if ($item['is_current'] === 1) {
                    $item['end_year'] = null;
                }

                $this->create($item);
            }
        });
    }
}
