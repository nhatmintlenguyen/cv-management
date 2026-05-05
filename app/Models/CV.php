<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class CV extends Model
{
    protected string $table = 'cvs';

    public function findByUserId(int $userId): ?array
    {
        return $this->firstWhere('user_id', $userId);
    }

    public function createForUser(int $userId, array $data): int
    {
        $data['user_id'] = $userId;

        return $this->create($data);
    }

    public function updateForUser(int $userId, array $data): bool
    {
        $cv = $this->findByUserId($userId);

        if ($cv === null) {
            return false;
        }

        return $this->update((int) $cv['id'], $data);
    }

    public function findWithLookups(int $cvId): ?array
    {
        return $this->first(
            'SELECT cvs.*,
                    cv_templates.name AS template_name,
                    cv_categories.name AS category_name,
                    genders.name AS gender_name,
                    countries.name AS country_name,
                    cities.name AS city_name,
                    districts.name AS district_name,
                    users.avatar_url
             FROM `cvs`
             INNER JOIN `users` ON users.id = cvs.user_id
             LEFT JOIN `cv_templates` ON cv_templates.id = cvs.cv_template_id
             INNER JOIN `cv_categories` ON cv_categories.id = cvs.cv_category_id
             INNER JOIN `genders` ON genders.id = cvs.gender_id
             INNER JOIN `countries` ON countries.id = cvs.country_id
             INNER JOIN `cities` ON cities.id = cvs.city_id
             LEFT JOIN `districts` ON districts.id = cvs.district_id
             WHERE cvs.id = :cv_id
             LIMIT 1',
            ['cv_id' => $cvId]
        );
    }

    public function findFullCV(int $cvId): ?array
    {
        $cv = $this->findWithLookups($cvId);

        if ($cv === null) {
            return null;
        }

        $cv['educations'] = (new CVEducation())->findByCvId($cvId);
        $cv['work_histories'] = (new CVWorkHistory())->findByCvId($cvId);
        $cv['certificates'] = (new CVCertificate())->findByCvId($cvId);
        $cv['skills'] = (new CVSkill())->findByCvId($cvId);

        return $cv;
    }
}
