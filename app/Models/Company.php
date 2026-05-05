<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Company extends Model
{
    protected string $table = 'companies';

    public function forEmployer(int $employerUserId): array
    {
        return $this->where('employer_user_id', $employerUserId);
    }

    public function findByEmployerAndName(int $employerUserId, string $name): ?array
    {
        return $this->first(
            'SELECT *
             FROM `companies`
             WHERE `employer_user_id` = :employer_user_id
               AND `name` = :name
             LIMIT 1',
            ['employer_user_id' => $employerUserId, 'name' => $name]
        );
    }

    public function createOrUpdateForEmployer(int $employerUserId, array $data): int
    {
        $name = trim((string) ($data['name'] ?? ''));
        $existing = $this->findByEmployerAndName($employerUserId, $name);

        $payload = [
            'name' => $name,
            'avatar_url' => $data['avatar_url'] ?? null,
            'description' => $data['description'] ?? null,
        ];

        if ($existing !== null) {
            $this->update((int) $existing['id'], $payload);
            return (int) $existing['id'];
        }

        $payload['employer_user_id'] = $employerUserId;

        return $this->create($payload);
    }
}
