<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class CVCertificate extends Model
{
    protected string $table = 'cv_certificates';

    public function findByCvId(int $cvId): array
    {
        return $this->get(
            'SELECT cv_certificates.*,
                    certificate_names.name AS certificate_name,
                    issuing_organizations.name AS issuing_organization_name
             FROM `cv_certificates`
             INNER JOIN `certificate_names` ON certificate_names.id = cv_certificates.certificate_name_id
             INNER JOIN `issuing_organizations` ON issuing_organizations.id = cv_certificates.issuing_organization_id
             WHERE cv_certificates.cv_id = :cv_id
             ORDER BY cv_certificates.display_order ASC, cv_certificates.year_issued DESC',
            ['cv_id' => $cvId]
        );
    }

    public function deleteByCvId(int $cvId): int
    {
        return $this->query(
            'DELETE FROM `cv_certificates` WHERE `cv_id` = :cv_id',
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
